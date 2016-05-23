<?php
namespace Concrete\Core\Device;

class DeviceServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{
    public function register()
    {
        \Core::bindShared('device/manager', function () {
            /** @var DeviceManagerInterface $device_manager */
            $device_manager = \Core::make('\\Concrete\\Core\\Device\\DeviceManager');

            $config = \Config::get('devices');
            foreach ($config as $device_handle => $device_config) {
                $class = \Config::get('device.device_class', '\\Concrete\\Core\Device\Device');
                if ($given_class = array_get($device_config, 'class')) {
                    if (class_exists($given_class)) {
                        $reflection = new \ReflectionClass($given_class);
                        if ($reflection->implementsInterface('\Concrete\Core\Device\DeviceInterface')) {
                            $class = $given_class;
                        }
                    }
                }

                $obj = call_user_func(array($class, 'configConstructor'), $device_handle, $device_config);
                if ($obj) {
                    $device_manager->register($obj);
                }
            }

            return $device_manager;
        });
    }
}
