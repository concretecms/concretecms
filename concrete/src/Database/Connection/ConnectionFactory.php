<?php
namespace Concrete\Core\Database\Connection;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Driver\DriverManager;
use Doctrine\DBAL\DBALException;

class ConnectionFactory
{
    /** @var Application */
    protected $app;

    /** @var DriverManager */
    protected $driver_manager;

    public function __construct(Application $app, DriverManager $driver_manager)
    {
        $this->app = $app;
        $this->driver_manager = $driver_manager;
    }

    public function createConnection($config)
    {
        $driver = $this->driver_manager->driver(array_get($config, 'driver', ''));

        if (!($driver instanceof \Doctrine\DBAL\Driver)) {
            $driver = $this->driver_manager->driver();
        }

        $params = $config;
        $params['host'] = array_get($params, 'host', array_get($config, 'server'));
        $params['user'] = array_get($params, 'user', array_get($config, 'username'));
        $params['driverOptions'] = [\PDO::MYSQL_ATTR_MULTI_STATEMENTS => false];
        $params['wrapperClass'] = array_get($config, 'wrapperClass', '\Concrete\Core\Database\Connection\Connection');
        unset($params['driver']);

        $wrapperClass = 'Doctrine\DBAL\Connection';
        if (isset($params['wrapperClass'])) {
            if (is_subclass_of($params['wrapperClass'], $wrapperClass)) {
                $wrapperClass = $params['wrapperClass'];
            } else {
                throw DBALException::invalidWrapperClass($params['wrapperClass']);
            }
        }

        return new $wrapperClass($params, $driver);
    }

    /**
     * @param $config
     * @param $name
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws DBALException
     */
    public function make($config, $name)
    {
        return $this->createConnection($config);
    }

    /**
     * @return DriverManager
     */
    public function getDriverManager()
    {
        return $this->driver_manager;
    }
}
