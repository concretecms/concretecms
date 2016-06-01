<?php
namespace Concrete\Core\Device;

class DeviceManager implements DeviceManagerInterface
{
    protected $container = array();

    /**
     * @param $handle
     *
     * @return DeviceInterface
     */
    public function get($handle)
    {
        return $this->container[$handle];
    }

    public function has($handle)
    {
        return isset($this->container[$handle]);
    }

    /**
     * @return DeviceInterface[]
     */
    public function getList()
    {
        return $this->container;
    }

    /**
     * @param Device $device
     */
    public function register(DeviceInterface $device)
    {
        $this->container[$device->getHandle()] = $device;
    }
}
