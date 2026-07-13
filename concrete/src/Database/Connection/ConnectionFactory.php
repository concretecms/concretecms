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

    /**
     * @param \ArrayAccess|array $config
     *
     * @return \Concrete\Core\Database\Connection\Connection|\Doctrine\DBAL\Connection
     */
    public function createConnection($config)
    {
        $driver = $this->driver_manager->driver(array_get($config, 'driver', ''));

        if (!($driver instanceof \Doctrine\DBAL\Driver)) {
            $driver = $this->driver_manager->driver();
        }

        $params = $this->normalizeConnectionParams($config);
        if (!isset($params['driverOptions'])) {
            $params['driverOptions'] = [];
        }
        if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
            $params['driverOptions'][\PDO::MYSQL_ATTR_MULTI_STATEMENTS] = false;
        }
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

        $connection =  new $wrapperClass($params, $driver);
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('json', 'json_array');
        return $connection;
    }

    /**
     * Normalize both the legacy Concrete configuration keys and Doctrine's
     * primary/replica configuration structure.
     *
     * The top-level connection settings are the defaults for the primary. An
     * explicit primary configuration overrides those defaults, and every
     * replica inherits the resulting primary configuration.
     *
     * @param \ArrayAccess|array $config
     *
     * @return array
     */
    private function normalizeConnectionParams($config)
    {
        $params = is_array($config) ? $config : iterator_to_array($config);
        $primaryOverrides = array_get($params, 'primary', []);
        $replicaOverrides = array_get($params, 'replica', []);

        unset($params['primary'], $params['replica']);
        $params = $this->normalizeEndpointParams($params);

        $endpointDefaults = $params;
        unset($endpointDefaults['wrapperClass'], $endpointDefaults['keepReplica']);

        $primary = array_replace(
            $endpointDefaults,
            $this->normalizeEndpointParams(is_array($primaryOverrides) ? $primaryOverrides : [])
        );

        $replicas = [];
        if (is_array($replicaOverrides)) {
            foreach ($replicaOverrides as $replica) {
                if (is_array($replica)) {
                    $replicas[] = array_replace($primary, $this->normalizeEndpointParams($replica));
                }
            }
        }
        if ($replicas === []) {
            $replicas[] = $primary;
        }

        // Keep the finalized primary values at the top level for compatibility
        // with DBAL and Concrete code that reads Connection::getParams().
        $params = array_replace($params, $primary);
        $params['primary'] = $primary;
        $params['replica'] = $replicas;

        return $params;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function normalizeEndpointParams(array $params)
    {
        if (!isset($params['host']) && isset($params['server'])) {
            $params['host'] = $params['server'];
        }
        if (!isset($params['server']) && isset($params['host'])) {
            $params['server'] = $params['host'];
        }
        if (!isset($params['user']) && isset($params['username'])) {
            $params['user'] = $params['username'];
        }
        if (!isset($params['username']) && isset($params['user'])) {
            $params['username'] = $params['user'];
        }
        if (!isset($params['database']) && isset($params['dbname'])) {
            $params['database'] = $params['dbname'];
        }
        if (!isset($params['dbname']) && isset($params['database'])) {
            $params['dbname'] = $params['database'];
        }

        return $params;
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
