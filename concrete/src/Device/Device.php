<?php
namespace Concrete\Core\Device;

/**
 * Class Device
 * A representation of a device
 *
 * @package Concrete\Core\Device
 */
class Device implements DeviceInterface
{

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $brand;

    /**
     * @var string
     */
    protected $orientation;

    /**
     * @var string
     */
    protected $agent;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var int The device pixel ratio
     */
    protected $ratio;

    /**
     * @var int Device::UNKNOWN|Device::MOBILE|Device::TABLET|Device::DESKTOP
     */
    protected $type;

    /**
     * @param string $handle A unique handle
     * @param string $name A display name
     * @param string $brand The brand to show
     * @param string $user_agent The device's user agent
     * @param int $width The device width in landscape
     * @param int $height The device height in landscape
     * @param int $type The device type Device::UNKNOWN|Device::MOBILE|Device::TABLET|Device::DESKTOP
     * @param int $ratio The device pixel ratio
     */
    public function __construct($handle, $name, $brand, $user_agent, $width, $height, $type, $ratio = 1)
    {
        $this->handle = $handle;
        $this->name = $name;
        $this->brand = $brand;
        $this->agent = $user_agent;
        $this->width = intval($width);
        $this->height = intval($height);
        $this->ratio = intval($ratio);
        $this->type = intval($type);
    }

    /**
     * Get the device handle
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get the device name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the device brand
     * @return string
     */
    public function getBrand()
    {
        return $this->name;
    }

    /**
     * Get the device user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->agent;
    }

    /**
     * Get the screen width of the device in pixels
     * Be sure to adjust this by the device pixel ratio
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the screen height of the device in pixels
     * Be sure to adjust this by the device pixel ratio
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the devices type
     * this is an int that maps to a constant on this class, UNKNOWN MOBILE TABLET or DESKTOP
     * If testing against a device and not against "UNKNOWN", do not test this directly against a device type, instead
     * use bitwise AND to test for the enum you'd like to test:
     *
     *     // Check if the type is known, if so is it mobile?
     *     if ($device->getType() == Device::UNKNOWN) {
     *         $is_mobile = !!($device->getPixelRatio() & Device::MOBILE);
     *     }
     *
     * http://php.net/manual/en/language.operators.bitwise.php
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the device pixel ratio
     *
     * @return int
     */
    public function getPixelRatio()
    {
        return $this->ratio;
    }

    /**
     * Get the HTML for this device's viewport
     * @return string
     */
    public function getViewportHTML()
    {
        $added_data = array(
            'handle',
            'name',
            'brand',
            'width',
            'height',
            'ratio',
            'agent');

        $datas = array();
        foreach ($added_data as $key) {
            $datas[] = sprintf('data-device-%s="%s"', $key, h($this->{$key}));
        }

        return sprintf(
            '<div class="ccm-device-viewport" %s style="width:%spx;height:%spx">%s</div>',
            implode(' ', $datas),
            floor($this->getWidth() / $this->getPixelRatio()),
            floor($this->getHeight() / $this->getPixelRatio()),
            '<iframe class="ccm-display-frame"></iframe>');
    }

    public function getIconClass()
    {
        $type = $this->getType();

        if ($type == self::UNKNOWN) {
            return 'ccm-device-icon-unknown fa fa-question';
        }

        if ($this->isMobile()) {
            return 'ccm-device-icon-mobile fa fa-mobile';
        }

        if ($this->isTablet()) {
            return 'ccm-device-icon-tablet fa fa-tablet';
        }

        if ($this->isDesktop()) {
            return 'ccm-device-icon-desktop fa fa-desktop';
        }
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        return !!($this->getType() & self::MOBILE);
    }

    /**
     * @return bool
     */
    public function isTablet()
    {
        return !!($this->getType() & self::TABLET);
    }

    /**
     * @return bool
     */
    public function isDesktop()
    {
        return !!($this->getType() & self::DESKTOP);
    }

    /**
     * Get the device's default orientation
     *
     * @return string ["landscape"|"portrait"]
     */
    public function getDefaultOrientation()
    {
        if ($this->orientation) {
            return $this->orientation;
        }

        if ($this->isMobile()) {
            return 'portrait';
        }

        return 'landscape';
    }

    public static function configConstructor($handle, array $config)
    {
        $name = array_get($config, 'name', $handle);
        $brand = array_get($config, 'brand', '');
        $agent = array_get($config, 'agent', '');

        $width = array_get($config, 'width', 0);
        $height = array_get($config, 'height', 0);
        $type = array_get($config, 'type', self::UNKNOWN);

        $device = new static($handle, $name, $brand, $agent, $width, $height, $type);

        if ($ratio = array_get($config, 'pixel_ratio')) {
            $device->ratio = $ratio;
        }

        if ($orientation = array_get($config, 'default_orientation')) {
            $device->orientation = $orientation;
        }

        return $device;
    }

}
