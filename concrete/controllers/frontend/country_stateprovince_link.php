<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Service\StatesProvincesList;

class CountryStateprovinceLink extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStateprovinces()
    {
        $result = [];
        $countryCode = $this->request->query->get('countryCode');
        if (is_string($countryCode)) {
            $countryCode = trim($countryCode);
            if ($countryCode !== '') {
                $statesprovinceslist = $this->app->make(StatesProvincesList::class);
                /* @var StatesProvincesList $statesprovinceslist */
                $list = $statesprovinceslist->getStateProvinceArray($countryCode);
                if ($list !== null) {
                    $result = [];
                    foreach ($list as $stateprovinceCode => $stateprovinceName) {
                        $result[] = [$stateprovinceCode, $stateprovinceName];
                    }
                }
            }
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
}
