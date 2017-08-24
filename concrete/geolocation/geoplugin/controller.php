<?php
namespace Concrete\Geolocator\Geoplugin;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Geolocator\GeolocationFailedException;
use Concrete\Core\Geolocator\GeolocationResult;
use Concrete\Core\Geolocator\GeolocatorController;
use Concrete\Core\Http\Client\Client as HttpClient;
use IPLib\Address\AddressInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Zend\Http\Client\Exception\RuntimeException as HttpClientException;

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
        $result = null;
        try {
            $response = $httpClient->send();
        } catch (HttpClientException $x) {
            $result = new GeolocationFailedException($this->geolocator, $address, t('Request to geoPlugin failed: %s', $x->getMessage()));
        }
        if ($result === null) {
            if ($response->getStatusCode() !== 200) {
                $result = new GeolocationFailedException($this->geolocator, $address, t('Request to geoPlugin failed with return code %s', sprintf('%s (%s)', $response->getStatusCode(), $response->getReasonPhrase())));
            } else {
                $responseBody = $response->getBody();
                $data = @json_decode($responseBody, true);
                if (
                    !is_array($data)
                    || empty($data['geoplugin_status'])
                ) {
                    $result = new GeolocationFailedException($this->geolocator, $address, t('Malformed data receivedfrom geoPlugin (%s)', $responseBody));
                } else {
                    switch ($data['geoplugin_status']) {
                        case static::GEOPLUGIN_STATUS_NOTFOUND:
                            $result = null;
                            break;
                        case static::GEOPLUGIN_STATUS_OK:
                        case static::GEOPLUGIN_STATUS_ONLYCOUNTRY:
                            $result = $this->dataToGeolocationResult($data, $configuration);
                            break;
                        default:
                            $result = new GeolocationFailedException($this->geolocator, $address, t('Unknown geoPlugin status code: %s', $data['geoplugin_status']));
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
     *
     * @return GeolocationResult
     */
    private function dataToGeolocationResult(array $data, array $configuration)
    {
        $result = new GeolocationResult();

        return $result
            ->setCityName(empty($configuration['skipCity']) ? $data['geoplugin_city'] : '')
            ->setStateProvinceCode(empty($configuration['skipStateProvince']) ? $data['geoplugin_regionCode'] : '')
            ->setStateProvinceName(empty($configuration['skipStateProvince']) ? $data['geoplugin_regionName'] : '')
            ->setCountryCode(empty($configuration['skipCountry']) ? $data['geoplugin_countryCode'] : '')
            ->setCountryName(empty($configuration['skipCountry']) ? $data['geoplugin_countryName'] : '')
            ->setLatitude(empty($configuration['skipLatitudeLongitude']) ? $data['geoplugin_latitude'] : '')
            ->setLongitude(empty($configuration['skipLatitudeLongitude']) ? $data['geoplugin_longitude'] : '')
        ;
    }
}
