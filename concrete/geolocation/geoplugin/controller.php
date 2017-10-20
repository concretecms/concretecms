<?php
namespace Concrete\Geolocator\Geoplugin;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Geolocator\GeolocationResult;
use Concrete\Core\Geolocator\GeolocatorController;
use Concrete\Core\Http\Client\Client as HttpClient;
use Exception;
use IPLib\Address\AddressInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class Controller extends GeolocatorController
{
    /**
     * OK, lookup no problem.
     *
     * @var int
     */
    const GEOPLUGIN_STATUS_OK = 200;

    /**
     * Lookup OK, but only country data returned, no city values found (a play on the http 206 partial content code).
     *
     * @var int
     */
    const GEOPLUGIN_STATUS_ONLYCOUNTRY = 206;

    /**
     * No data found for the IP at all.
     *
     * @var int
     */
    const GEOPLUGIN_STATUS_NOTFOUND = 404;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Geolocator\GeolocatorControllerInterface::saveConfigurationForm()
     */
    public function saveConfigurationForm(array $configuration, ParameterBag $data, ErrorList $error)
    {
        $url = $data->get('geoplugin-url');
        $url = is_string($url) ? trim($url) : '';
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            $error->add(t('Please specify a valid URL'));
        } elseif (strpos($url, '[[IP]]') === false) {
            $error->add(t('The URL must contain the %s placeholder', '[[IP]]'));
        } else {
            $configuration['url'] = $url;
        }
        $configuration['skipCity'] = $data->get('geoplugin-trust-city') ? false : true;
        $configuration['skipStateProvince'] = $data->get('geoplugin-trust-stateprovince') ? false : true;
        $configuration['skipCountry'] = $data->get('geoplugin-trust-country') ? false : true;
        $configuration['skipLatitudeLongitude'] = $data->get('geoplugin-trust-latlon') ? false : true;

        return $configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Geolocator\GeolocatorController::performGeolocation()
     */
    protected function performGeolocation(AddressInterface $address)
    {
        $configuration = $this->geolocator->getGeolocatorConfiguration();
        $uri = str_replace('[[IP]]', rawurlencode((string) $address), $configuration['url']);
        $httpClient = $this->app->make(HttpClient::class);
        $httpClient->setUri($uri);
        $result = new GeolocationResult();
        try {
            $response = $httpClient->send();
        } catch (Exception $x) {
            $result->setError(GeolocationResult::ERR_NETWORK, t('Request to geoPlugin failed: %s', $x->getMessage()), $x);
        }
        if ($result->hasError() === false) {
            if (!$response->isSuccess()) {
                $result->setError(GeolocationResult::ERR_NETWORK, t('Request to geoPlugin failed with return code %s', sprintf('%s (%s)', $response->getStatusCode(), $response->getReasonPhrase())));
            } else {
                $responseBody = $response->getBody();
                $data = @json_decode($responseBody, true);
                if (
                    !is_array($data)
                    || empty($data['geoplugin_status'])
                ) {
                    $result->setError(GeolocationResult::ERR_LIBRARYSPECIFIC, t('Malformed data received from geoPlugin (%s)', $responseBody));
                } else {
                    switch ($data['geoplugin_status']) {
                        case static::GEOPLUGIN_STATUS_NOTFOUND:
                            break;
                        case static::GEOPLUGIN_STATUS_OK:
                        case static::GEOPLUGIN_STATUS_ONLYCOUNTRY:
                            $this->dataToGeolocationResult($data, $configuration, $result);
                            break;
                        default:
                            $result->setError(GeolocationResult::ERR_LIBRARYSPECIFIC, t('Unknown geoPlugin status code: %s', $data['geoplugin_status']));
                            break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @param array configuration
     * @param GeolocationResult $result
     */
    private function dataToGeolocationResult(array $data, array $configuration, GeolocationResult $result)
    {
        $result
            ->setCityName(empty($configuration['skipCity']) ? $data['geoplugin_city'] : '')
            ->setStateProvinceCode(empty($configuration['skipStateProvince']) ? $data['geoplugin_regionCode'] : '')
            ->setStateProvinceName(empty($configuration['skipStateProvince']) ? $data['geoplugin_regionName'] : '')
            ->setCountryCode(empty($configuration['skipCountry']) ? $data['geoplugin_countryCode'] : '')
            ->setCountryName(empty($configuration['skipCountry']) ? $data['geoplugin_countryName'] : '')
            ->setLatitude(empty($configuration['skipLatitudeLongitude']) ? $data['geoplugin_latitude'] : null)
            ->setLongitude(empty($configuration['skipLatitudeLongitude']) ? $data['geoplugin_longitude'] : null)
        ;
    }
}
