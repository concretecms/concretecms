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
    protected $defaultCountry = '';

    /**
     * @Column(type="boolean")
     */
    protected $hasCustomCountries = false;

    /**
     * @Column(type="json_array")
     */
    protected $customCountries = array();

    /**
     * @return mixed
     */
    public function getDefaultCountry()
    {
        return $this->defaultCountry;
    }

    /**
     * @param mixed $defaultCountry
     */
    public function setDefaultCountry($defaultCountry)
    {
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @return mixed
     */
    public function hasCustomCountries()
    {
        return $this->hasCustomCountries;
    }

    /**
     * @param mixed $hasCustomCountries
     */
    public function setHasCustomCountries($hasCustomCountries)
    {
        $this->hasCustomCountries = $hasCustomCountries;
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

    public function createController()
    {
        $controller = new \Concrete\Attribute\Address\Controller($this->getAttributeType());

        return $controller;
    }
}
