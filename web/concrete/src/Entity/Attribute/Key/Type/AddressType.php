<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;

/**
 * @Entity
 * @Table(name="AddressAttributeKeyTypes")
 */
class AddressType extends Type
{
    public function getAttributeValue()
    {
        return new AddressValue();
    }

    /**
     * @Column(type="string")
     */
    protected $akDefaultCountry = '';

    /**
     * @Column(type="boolean")
     */
    protected $akHasCustomCountries = false;

    /**
     * @Column(type="json_array")
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
