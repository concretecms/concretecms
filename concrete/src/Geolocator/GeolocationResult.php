<?php
namespace Concrete\Core\Geolocator;

use Exception;
use JsonSerializable;
use Punic\Territory;

class GeolocationResult implements JsonSerializable
{
    /**
     * Error category: no error.
     *
     * @var int
     */
    const ERR_NONE = 0;

    /**
     * Error category: other/unknown error category.
     *
     * @var int
     */
    const ERR_OTHER = -1;

    /**
     * Error category: no current geolocation library.
     *
     * @var int
     */
    const ERR_NOCURRENTLIBRARY = 1;

    /**
     * Error category: the IP address of the current client is not available.
     *
     * @var int
     */
    const ERR_NOCURRENTIPADDRESS = 2;

    /**
     * Error category: the IP address is not in the public IP address ranges.
     *
     * @var int
     */
    const ERR_IPNOTPUBLIC = 3;

    /**
     * Error category: the geolocation library has a wrong configuration.
     *
     * @var int
     */
    const ERR_MISCONFIGURED = 4;

    /**
     * Error category: a network error occurred.
     *
     * @var int
     */
    const ERR_NETWORK = 5;

    /**
     * Error category: a library-specific error occurred.
     *
     * @var int
     */
    const ERR_LIBRARYSPECIFIC = 6;

    /**
     * The error category (one of the GeolocationResult::ERR_... constants).
     *
     * @var int
     */
    protected $errorCode = self::ERR_NONE;

    /**
     * The error message.
     *
     * @var string
     */
    protected $errorMessage = '';

    /**
     * The underlying exception causing the error (if available).
     *
     * @var Exception|null
     */
    protected $innerException = null;

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
     * Set the error state.
     *
     * @param int $code one of the GeolocationResult::ERR_... constants
     * @param string $message the error message
     * @param Exception|null $innerException the underlying exception causing the error (if available)
     */
    public function setError($code, $message = '', Exception $innerException = null)
    {
        if ($code == static::ERR_NONE && !$message && $innerException === null) {
            $this->errorCode = static::ERR_NONE;
            $this->errorMessage = '';
            $this->innerException = null;
        } else {
            $code = (int) $code;
            $this->errorCode = $code === static::ERR_NONE ? static::ERR_OTHER : $code;
            $message = trim((string) $message);
            if ($message === '') {
                switch ($this->errorCode) {
                    case static::ERR_NOCURRENTLIBRARY:
                        $this->errorMessage = t("There's no current geolocation library");
                        break;
                    case static::ERR_NOCURRENTIPADDRESS:
                        $this->errorMessage = t('The IP address of the current client is not available');
                        break;
                    case static::ERR_IPNOTPUBLIC:
                        $this->errorMessage = t('The IP address is not in the public IP address ranges');
                        break;
                    case static::ERR_MISCONFIGURED:
                        $this->errorMessage = t('The geolocation library is misconfigured');
                        break;
                    case static::ERR_NETWORK:
                        $this->errorMessage = t('A network error occurred during the geolocalization');
                        break;
                    case static::ERR_LIBRARYSPECIFIC:
                        $this->errorMessage = t('An unspecified error occurred in the geolocation library');
                        break;
                    default:
                        $this->errorMessage = t('An unexpected error occurred in the geolocation library');
                }
            } else {
                $this->errorMessage = $message;
            }
            $this->innerException = $innerException;
        }
    }

    /**
     * Does an error occurred?
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->errorCode !== static::ERR_NONE;
    }

    /**
     * Get the error category (one of the GeolocationResult::ERR_... constants).
     *
     * @return int return GeolocationResult::ERR_NONE if and only if there's no error
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get the error message ().
     *
     * @return string return an empty string if and only if there's no error
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Get the underlying exception causing the error (if available).
     *
     * @return Exception|null
     */
    public function getInnerException()
    {
        return $this->innerException;
    }

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
     * @param bool $resolveFromCodeIfUnavailable If the Country name is not set, should we try to derive it from the Country code?
     *
     * @return string
     */
    public function getCountryName($resolveFromCodeIfUnavailable = false)
    {
        $result = $this->countryName;
        if ($result === '' && $resolveFromCodeIfUnavailable && $this->countryCode !== '') {
            $name = Territory::getName($this->countryCode, 'en_US');
            if ($name !== $this->countryCode) {
                $result = $name;
            }
        }

        return $result;
    }

    /**
     * Get the name of the country (in the current language).
     *
     * @return string
     */
    public function getCountryNameLocalized()
    {
        $result = '';
        if ($this->countryCode !== '') {
            $localized = Territory::getName($this->countryCode);
            if ($localized !== $this->countryCode) {
                $result = $localized;
            }
        }
        if ($result === '') {
            $result = $this->countryName;
        }

        return $result;
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
     * @return float|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the latitude.
     *
     * @param float|null $value
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
     * @return float|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the longitude.
     *
     * @param float|null $value
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
     * Does this instance contain some geolocalized data?
     *
     * @return bool
     */
    public function hasData()
    {
        return
            $this->countryCode !== ''
            ||
            $this->countryName !== ''
            ||
            $this->stateProvinceCode !== ''
            ||
            $this->stateProvinceName !== ''
            ||
            $this->cityName !== ''
            ||
            $this->postalCode !== ''
            ||
            $this->latitude !== null
            ||
            $this->longitude !== null
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return [
            'error' => $this->hasError() ? ['code' => $this->getErrorCode(), 'message' => $this->getErrorMessage()] : null,
            'hasData' => $this->hasData(),
            'cityName' => $this->getCityName(),
            'stateProvinceCode' => $this->getStateProvinceCode(),
            'stateProvinceName' => $this->getStateProvinceName(),
            'postalCode' => $this->getPostalCode(),
            'countryCode' => $this->getCountryCode(),
            'countryName' => $this->getCountryName(true),
            'countryNameLocalized' => $this->getCountryNameLocalized(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
        ];
    }
}
