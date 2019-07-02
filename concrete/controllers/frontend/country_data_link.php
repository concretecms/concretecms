<?php
namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Service\AddressFormat;
use Concrete\Core\Localization\Service\StatesProvincesList;

class CountryDataLink extends AbstractController
{
    public function getAll()
    {
        $countryCode = $this->request->query->get('countryCode');
        $statesProvinces = $this->getStatesProvicesList($countryCode);

        $result = ['statesProvices' => $statesProvinces];

        $af = $this->app->make(AddressFormat::class);
        $usedFields = $af->getCountryAddressUsedFields($countryCode);
        $requiredFields = $af->getCountryAddressRequiredFields($countryCode);
        $result['addressUsedFields'] = $usedFields;
        $result['addressRequiredFields'] = $requiredFields;

        return $this->app->make(ResponseFactoryInterface::class)->json(
            $result,
            200,
            [
                'Cache-Control' => 'public, max-age=7200',
                'Expires' => gmdate('r', time() + 7200),
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStateprovinces()
    {
        $result = [];

        $countryCode = $this->request->query->get('countryCode');
        foreach ($this->getStatesProvicesList($countryCode) as $code => $name) {
            $result[] = [$code, $name];
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(
            $result,
            200,
            [
                'Cache-Control' => 'public, max-age=7200',
                'Expires' => gmdate('r', time() + 7200),
            ]
        );
    }

    protected function getStatesProvicesList($countryCode)
    {
        $result = [];
        if (is_string($countryCode)) {
            $countryCode = trim($countryCode);
            if ($countryCode !== '') {
                $statesprovinceslist = $this->app->make(StatesProvincesList::class);
                /* @var StatesProvincesList $statesprovinceslist */
                $list = $statesprovinceslist->getStateProvinceArray($countryCode);
                if ($list !== null) {
                    $result = [];
                    foreach ($list as $stateprovinceCode => $stateprovinceName) {
                        $result[$stateprovinceCode] = $stateprovinceName;
                    }
                }
            }
        }

        return $result;
    }
}
