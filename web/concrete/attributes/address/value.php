<?php
namespace Concrete\Attribute\Address;

use Loader;
use \Concrete\Core\Foundation\Object;

class Value extends Object
{

    public static function getByID($avID)
    {
        $db = Loader::db();
        $value = $db->GetRow(
            "select avID, address1, address2, city, state_province, postal_code, country from atAddress where avID = ?",
            array($avID)
        );
        $aa = new Value();
        $aa->setPropertiesFromArray($value);
        if ($value['avID']) {
            return $aa;
        }
    }

    public function __construct()
    {
        $h = Loader::helper('lists/countries');
        $this->countryFull = $h->getCountryName($this->country);
    }

    public function getAddress1()
    {
        return $this->address1;
    }

    public function getAddress2()
    {
        return $this->address2;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getStateProvince()
    {
        return $this->state_province;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPostalCode()
    {
        return $this->postal_code;
    }

    public function getFullCountry()
    {
        $h = Loader::helper('lists/countries');
        return $h->getCountryName($this->country);
    }

    public function getFullStateProvince()
    {
        $h = Loader::helper('lists/states_provinces');
        $val = $h->getStateProvinceName($this->state_province, $this->country);
        if ($val == '') {
            return $this->state_province;
        } else {
            return $val;
        }
    }

    public function __toString()
    {
        $ret = '';
        if ($this->address1) {
            $ret .= $this->address1 . "\n";
        }
        if ($this->address2) {
            $ret .= $this->address2 . "\n";
        }
        if ($this->city) {
            $ret .= $this->city;
        }
        if ($this->city && $this->state_province) {
            $ret .= ", ";
        }
        if ($this->state_province) {
            $ret .= $this->getFullStateProvince();
        }
        if ($this->postal_code) {
            $ret .= " " . $this->postal_code;
        }
        if ($this->city || $this->state_province || $this->postal_code) {
            $ret .= "\n";
        }
        if ($this->country) {
            $ret .= $this->getFullCountry();
        }
        return $ret;
    }
}