<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Error\ErrorList\ErrorList;
use Symfony\Component\HttpFoundation\ParameterBag;
use IPLib\Address\AddressInterface;

interface GeolocatorControllerInterface
{
    /**
     * Does this geolocator library has a configuration form?
     *
     * @return bool
     */
    public function hasConfigurationForm();

    /**
     * Render the configuration form?
     */
    public function renderConfigurationForm();

    /**
     * Save the configuration form.
     *
     * @param array $configuration The initial geolocator configuration
     * @param ParameterBag $data The data received
     * @param ErrorList $error Add errors to this instance
     *
     * @return array The final geolocator configuration
     */
    public function saveConfigurationForm(array $configuration, ParameterBag $data, ErrorList $error);

    /**
     * Geolocate an IP address.
     *
     * @param AddressInterface $address
     *
     * @return GeolocationResult|GeolocationFailedException|null returns NULL if the geolocation is not applicable, a GeolocationFailedException if the geolocation failed, or a GeolocationResult otherwise
     */
    public function geolocateIPAddress(AddressInterface $address);
}
