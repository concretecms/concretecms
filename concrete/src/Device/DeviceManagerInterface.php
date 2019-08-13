<?php
namespace Concrete\Core\Device;

/**
 * @since 5.7.5
 */
interface DeviceManagerInterface
{
    public function get($handle);

    public function has($handle);

    public function getList();

    public function register(DeviceInterface $device);
}
