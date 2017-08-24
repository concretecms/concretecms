<?php
namespace Concrete\Core\Geolocator;

use JsonSerializable;

class GeolocationResult implements JsonSerializable
{
    /**
     * The city name.
     *
     * @var string
     */
    protected $cityName = '';

    /**
     * The code of the province/state.
     *
     * @var string
     */
    protected $stateProvinceCode = '';

    /**
     * The name of the province/state (in American English).
     *
     * @var string
     */
    protected $stateProvinceName = '';

    /**
     * The postal code.
     *
     * @var string
     */
    protected $postalCode = '';

    /**
     * The code of the country (two letter upper case ISO-3166 code).
     *
     * @var string
     */
    protected $countryCode = '';

    /**
     * The name of the country (in American English).
     *
     * @var string
     */
    protected $countryName = '';

    /**
     * The latitude.
     *
     * @var float|null
     */
    protected $latitude = null;

    /**
     * The longitude.
     *
     * @var float|null
     */
    protected $longitude = null;

    /**
     * Get the city name.
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Set the city name.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setCityName($value)
    {
        $this->cityName = (string) $value;

        return $this;
    }

    /**
     * Get the code of the province/state.
     *
     * @return string
     */
    public function getStateProvinceCode()
    {
        return $this->stateProvinceCode;
    }

    /**
     * Set the code of the province/state.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setStateProvinceCode($value)
    {
        $this->stateProvinceCode = (string) $value;

        return $this;
    }

    /**
     * Get the name of the province/state (in American English).
     *
     * @return string
     */
    public function getStateProvinceName()
    {
        return $this->stateProvinceName;
    }

    /**
     * Set the name of the province/state (in American English).
     *
     * @param string $value
     *
     * @return $this
     */
    public function setStateProvinceName($value)
    {
        $this->stateProvinceName = (string) $value;

        return $this;
    }

    /**
     * Get the postal code.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set the postal code.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setPostalCode($value)
    {
        $this->postalCode = (string) $value;

        return $this;
    }

    /**
     * Get the code of the country (two letter upper case ISO-3166 code).
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the code of the country (two letter upper case ISO-3166 code).
     *
     * @param string $value
     *
     * @return $this
     */
    public function setCountryCode($value)
    {
        $this->countryCode = (string) $value;

        return $this;
    }

    /**
     * Get the name of the country (in American English).
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Set the name of the country (in American English).
     *
     * @param string $value
     *
     * @return $this
     */
    public function setCountryName($value)
    {
        $this->countryName = (string) $value;

        return $this;
    }

    /**
     * Get the latitude.
     *
     * @return int|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the latitude.
     *
     * @param int|null $value
     *
     * @return $this
     */
    public function setLatitude($value)
    {
        if (is_float($value) || is_int($value) || (is_string($value) && is_numeric($value))) {
            $this->latitude = (float) $value;
        } else {
            $this->latitude = null;
        }

        return $this;
    }

    /**
     * Get the longitude.
     *
     * @return int|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the longitude.
     *
     * @param int|null $value
     *
     * @return $this
     */
    public function setLongitude($value)
    {
        if (is_float($value) || is_int($value) || (is_string($value) && is_numeric($value))) {
            $this->longitude = (float) $value;
        } else {
            $this->longitude = null;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return [
            'cityName' => $this->cityName,
            'stateProvinceCode' => $this->stateProvinceCode,
            'stateProvinceName' => $this->stateProvinceName,
            'postalCode' => $this->postalCode,
            'countryCode' => $this->countryCode,
            'countryName' => $this->countryName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
