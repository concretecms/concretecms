<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\Localization\Service\AddressFormat;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atAddress")
 */
class AddressValue extends AbstractValue
{
    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $address1;

    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $address2;

    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $address3;

    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $city;

    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $state_province;

    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $country;

    /**
     * Note: It's the public portion of this property that is deprecated.
     *
     * @deprecated
     * @ORM\Column(type="string", nullable=true)
     */
    public $postal_code;

    /**
     * @return mixed
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param mixed $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return mixed
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param mixed $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return mixed
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * @param mixed $address3
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getStateProvince()
    {
        return $this->state_province;
    }

    /**
     * @param mixed $state_province
     */
    public function setStateProvince($state_province)
    {
        $this->state_province = $state_province;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @param mixed $postal_code
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;
    }

    public function getFullCountry()
    {
        $h = \Core::make('helper/lists/countries');

        return $h->getCountryName($this->country);
    }

    public function getFullStateProvince()
    {
        $h = \Core::make('helper/lists/states_provinces');
        $val = $h->getStateProvinceName($this->state_province, $this->country);
        if ($val == '') {
            return $this->state_province;
        } else {
            return $val;
        }
    }

    public function __toString()
    {
        $valueData = [
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'state_province' => $this->state_province,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
        ];

        $app = Application::getFacadeApplication();
        $af = $app->make(AddressFormat::class);

        return $af->format($valueData, 'text');
    }
}
