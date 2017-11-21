<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atAddressSettings")
 */
class AddressSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akDefaultCountry = '';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $akHasCustomCountries = false;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $customCountries = [];

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $akGeolocateCountry = false;

    /**
     * @return mixed
     */
    public function getDefaultCountry()
    {
        return $this->akDefaultCountry;
    }

    /**
     * @param mixed $defaultCountry
     */
    public function setDefaultCountry($defaultCountry)
    {
        $this->akDefaultCountry = $defaultCountry;
    }

    /**
     * @return mixed
     */
    public function hasCustomCountries()
    {
        return $this->akHasCustomCountries;
    }

    /**
     * @param mixed $hasCustomCountries
     */
    public function setHasCustomCountries($hasCustomCountries)
    {
        $this->akHasCustomCountries = $hasCustomCountries;
    }

    /**
     * @return mixed
     */
    public function getCustomCountries()
    {
        return $this->customCountries;
    }

    /**
     * @param mixed $customCountries
     */
    public function setCustomCountries($customCountries)
    {
        $this->customCountries = $customCountries;
    }

    /**
     * Should we try to determine the Country starting from the visitor's IP address?
     *
     * @return bool
     */
    public function geolocateCountry()
    {
        return $this->akGeolocateCountry;
    }

    /**
     * Should we try to determine the Country starting from the visitor's IP address?
     *
     * @param bool $value
     */
    public function setGeolocateCountry($value)
    {
        $this->akGeolocateCountry = (bool) $value;
    }
}
