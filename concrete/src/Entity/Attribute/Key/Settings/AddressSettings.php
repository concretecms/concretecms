<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;
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
    protected $customCountries = array();

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

}
