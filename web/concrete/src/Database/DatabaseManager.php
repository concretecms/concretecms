<?php
namespace Concrete\Core\Database;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Config;

class DatabaseManager
{

    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The database connection factory instance.
     *
     * @var \Concrete\Core\Database\Connection\ConnectionFactory
     */
    protected $factory;

    /**
     * The active connection instances.
     *
     * @var Connection[]
     */
    protected $connections = array();

    /**
     * The custom connection resolvers.
     *
     * @var array
     */
    protected $extensions = array();

    /**
     * Create a new database manager instance.
     *
     * @param Application                                          $app
     * @param \Concrete\Core\Database\Connection\ConnectionFactory $factory
     */
    public function __construct(Application $app, \Concrete\Core\Database\Connection\ConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;

        if ($this->app['config']['site_install.database']) {
            $this->app['config']['database'] = array_replace_recursive(
                $this->app['config']['site_install.database'],
                $this->app['config']['database']);
        }
    }

    /**
     * Legacy entry point
     *
     * @deprecated
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public function getActiveConnection()
    {
        return $this->connection();
    }

    /**
     * Legacy entry point
     *
     * @deprecated
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public function get()
    {
        return $this->connection();
    }

    /**
     * Get a database connection instance.
     *
     * @param  string $name
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();
        // If we haven't created this connection, we'll create it based on the config
        // provided in the application. Once we've created the connections we will
        // set the "fetch mode" for PDO which determines the query return types.
        if (!isset($this->connections[$name])) {
            $connection = $this->makeConnection($name);
            if (Config::get('concrete.log.queries.log')) {
                $connection->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());
            }
            $this->connections[$name] = $this->prepare($connection);
        }

        return $this->connections[$name];
    }

    /**
     * Disconnect from the given database and remove from local cache.
     *
     * @param  string $name
     * @return void
     */
    public function purge($name = null)
    {
        $this->disconnect($name);
        if (isset($this->connections[$name = $name ?: $this->getDefaultConnection()])) {
            unset($this->connections[$name]);
        }
    }

    /**
     * Disconnect from the given database.
     *
     * @param  string $name
     * @return void
     */
    public function disconnect($name = null)
    {
        if (isset($this->connections[$name = $name ?: $this->getDefaultConnection()])) {
            $this->connections[$name]->close();
        }
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string $name
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public function reconnect($name = null)
    {
        $this->disconnect($name = $name ?: $this->getDefaultConnection());

        if (!isset($this->connections[$name])) {
            return $this->connection($name);
        } else {
            return $this->refreshPdoConnections($name);
        }
    }

    /**
     * Refresh the PDO connections on a given connection.
     *
     * @param  string $name
     * @return Connection
     */
    protected function refreshPdoConnections($name)
    {
        $this->purge($name);
        return $this->connection($name);
    }

    /**
     * Make the database connection instance.
     *
     * @param  string $name
     * @return Connection
     */
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);

        // First we will check by the connection name to see if an extension has been
        // registered specifically for that connection. If it has we will call the
        // Closure and pass it the config allowing it to resolve the connection.
        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }

        $driver = $config['driver'];

        // Next we will check to see if an extension has been registered for a driver
        // and will call the Closure if so, which allows us to have a more generic
        // resolver for the drivers themselves which applies to all connections.
        if (isset($this->extensions[$driver])) {
            return call_user_func($this->extensions[$driver], $config, $name);
        }

        return $this->factory->make($config, $name);
    }

    /**
     * Prepare the database connection instance.
     *
     * @param Connection $connection
     * @return Connection
     */
    protected function prepare($connection)
    {
        return $connection;
    }

    /**
     * Get the configuration for a connection.
     *
     * @param  string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        // To get the database connection configuration, we will just pull each of the
        // connection configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.
        $connections = $this->app['config']['database.connections'];

        if (is_null($config = array_get($connections, $name))) {
            throw new \InvalidArgumentException("Database [$name] not configured.");
        }

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->app['config']['database.default-connection'];
    }

    /**
     * Set the default connection name.
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultConnection($name)
    {
        $this->app['config']['database.default-connection'] = $name;
    }

    /**
     * Register an extension connection resolver.
     *
     * @param  string   $name
     * @param  callable $resolver
     * @return void
     */
    public function extend($name, $resolver)
    {
        $this->extensions[$name] = $resolver;
    }

    /**
     * Return all of the created connections.
     *
     * @return \Concrete\Core\Database\Connection\Connection[]
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->connection(), $method), $parameters);
    }

    /**
     * @return \Concrete\Core\Database\Connection\ConnectionFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

}
