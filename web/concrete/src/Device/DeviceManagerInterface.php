<?php
namespace Concrete\Core\Device;

interface DeviceManagerInterface
{
    public function get($handle);

    public function has($handle);

    public function getList();

    public function register(DeviceInterface $device);
}