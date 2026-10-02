<?php
namespace Concrete\Core\Database\Driver;

use Illuminate\Support\Manager;

class DriverManager extends Manager
{
    /**
     * The array of created "drivers".
     *
     * @var \Doctrine\DBAL\Driver[]
     */
    protected $drivers = array();

    /**
     * @param string|null $driver the handle of the driver (if NULL, the default driver is used)
     *
     * @return \Doctrine\DBAL\Driver|mixed the driver created by the registered driver creator (it should be a \Doctrine\DBAL\Driver instance)
     */
    public function driver($driver = null)
    {
        return parent::driver($driver);
    }

    /**
     * @return \Doctrine\DBAL\Driver[]
     */
    public function getDrivers()
    {
        return parent::getDrivers();
    }

    /**
     * @param array $config Always database.drivers
     */
    public function configExtensions(array $config)
    {
        foreach ($config as $driver => $class) {
            $this->extend(
                $driver,
                function () use ($class) {
                    return new $class();
                });
        }
    }

    public function getDefaultDriver()
    {
        return 'concrete_pdo_mysql';
    }
}
