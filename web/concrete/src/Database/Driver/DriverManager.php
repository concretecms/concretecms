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
     * @param null $driver
     *
     * @return \Doctrine\DBAL\Driver
     */
    public function driver($driver = null)
    {
        return parent::driver($driver);
    }

    /**
     * @return \Doctrine\DBAL\Driver
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
        return 'c5_pdo_mysql';
    }
}
