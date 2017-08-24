<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Error\UserMessageException;
use IPLib\Address\AddressInterface;

class GeolocationFailedException extends UserMessageException
{
    /**
     * The failing geolocator library.
     *
     * @var Geolocator
     */
    protected $geolocator;

    /**
     * The failing IP address.
     *
     * @var AddressInterface
     */
    protected $ip;

    /**
     * Initialize the instance.
     *
     * @param Geolocator $geolocator the failing geolocator library
     * @param AddressInterface $ip the failing IP address
     * @param string $message the description of the failure
     */
    public function __construct(Geolocator $geolocator, AddressInterface $ip, $message)
    {
        $this->geolocator = $geolocator;
        $this->ip = $ip;
        parent::__construct($message);
    }

    /**
     * Get the failing geolocator library.
     *
     * @return Geolocator
     */
    public function getGeolocator()
    {
        return $this->geolocator;
    }

    /**
     * Get the failing IP address.
     *
     * @return AddressInterface
     */
    public function getIp()
    {
        return $this->ip;
    }
}
