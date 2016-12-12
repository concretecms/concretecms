<?php
namespace Concrete\Core\Device;

/**
 * Class Device
 * A representation of a device
 *
 * @package Concrete\Core\Device
 */
interface DeviceInterface
{

    /**
     * Device type constants, combine them like so:
     *
     *     $device = new Device('Microsoft Surface', 1366, 768, Device::TABLET | Device::DESKTOP, 1.5);
     *
     * and you can test against them like this:
     *
     *     $type = $device->getType();
     *     // Is the type known, and if so is it mobile?
     *     if ($type != Device::UNKNOWN && $type & Device::MOBILE) {
     *         // Mobile
     *     }
     */
    const UNKNOWN = 0;
    const MOBILE = 1;
    const TABLET = 2;
    const DESKTOP = 4;

    /**
     * Get the device handle
     *
     * @return string
     */
    public function getHandle();

    /**
     * Get the device name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the device brand
     *
     * @return string
     */
    public function getBrand();

    /**
     * Get the device user agent
     *
     * @return string
     */
    public function getUserAgent();

    /**
     * Get the screen width of the device in pixels
     * Be sure to adjust this by the device pixel ratio
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the screen height of the device in pixels
     * Be sure to adjust this by the device pixel ratio
     *
     * @return int
     */
    public function getHeight();

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
    public function getType();

    /**
     * Get the device pixel ratio
     *
     * @return int
     */
    public function getPixelRatio();

    /**
     * Get the HTML for this device's viewport
     * @return string
     */
    public function getViewportHTML();

    /**
     * Get the class to be used for this device's icon
     *
     * @return string
     */
    public function getIconClass();

    /**
     * @return bool
     */
    public function isMobile();

    /**
     * @return bool
     */
    public function isTablet();

    /**
     * @return bool
     */
    public function isDesktop();

    /**
     * Get the device's default orientation
     *
     * @return string ["landscape"|"portrait"]
     */
    public function getDefaultOrientation();

    /**
     * Construct from given configuration
     *
     * @param $handle
     * @param array $config
     * @return static
     */
    public static function configConstructor($handle, array $config);

}