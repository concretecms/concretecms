<?php

namespace Concrete\Core\Session;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\Request;
use Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler;
use Illuminate\Support\Str;
use Memcached;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Class SessionFactory
 * Base concrete5 session factory.
 *
 * To add custom handlers, extend this class and for a handler named "custom_test"
 * create a protected method `getCustomTestHandler`
 */
class SessionFactory implements SessionFactoryInterface
{

    protected $app;

    /**
     * The request object
     * We needed a reference to this object so that we could assign the session object to it.
     * Instead we are using the $app container to resolve the request at the time the session is created.
     * This makes testing a little harder, but ensures we apply the session object to the most accurate request instance.
     * Ideally neither would be required, as the operation creating the session would manage associating the two.
     *
     * @var \Concrete\Core\Session\Request
     * @deprecated
     */
    protected $request;

    /**
     * SessionFactory constructor.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Http\Request $request @deprecated, will be removed
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Create a new symfony session object
     * This method MUST NOT start the session.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createSession()
    {
        $config = $this->app['config']['concrete.session'];
        $storage = $this->getSessionStorage($config);

        // We have to use "build" here because we have bound this classname to this factory method
        $session = $this->app->build(SymfonySession::class, [$storage]);
        $session->setName(array_get($config, 'name'));

        /** @TODO Remove this call. We should be able to set this against the request somewhere much higher than this */
        /**       At the very least we should have an observer that can track the session status and set this */
        $this->app->make(\Concrete\Core\Http\Request::class)->setSession($session);

        return $session;
    }

    /**
     * Create and return a newly built file session handler
     *
     * @param array $config The `concrete.session` config item
     *
     * @return \Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler
     */
    protected function getFileHandler(array $config)
    {
        return $this->app->make(NativeFileSessionHandler::class, [
            array_get($config, 'save_path')
        ]);
    }

    /**
     * Create a new database session handler to handle session
     *
     * @param array $config The `concrete.session` config item
     *
     * @return \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler
     */
    protected function getDatabaseHandler(array $config)
    {
        return $this->app->make(PdoSessionHandler::class, [
            $this->app->make(Connection::class)->getWrappedConnection(),
            [
                'db_table' => 'Sessions',
                'db_id_col' => 'sessionID',
                'db_data_col' => 'sessionValue',
                'db_time_col' => 'sessionTime',
                'db_lifetime_col' => 'sessionLifeTime',
                'lock_mode' => PdoSessionHandler::LOCK_ADVISORY
            ]
        ]);
    }

    /**
     * Return a built Memcached session handler
     *
     * @param array $config The `concrete.session` config item
     *
     * @return \Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler
     */
    protected function getMemcachedHandler(array $config)
    {
        // Create new memcached instance
        $memcached = $this->app->make(Memcached::class, [
            'CCM_SESSION',
            null
        ]);

        $servers = array_get($config, 'servers', []);

        // Add missing servers
        foreach ($this->newMemcachedServers($memcached, $servers) as $server) {
            $memcached->addServer(
                array_get($server, 'host'),
                array_get($server, 'port'),
                array_get($server, 'weight')
            );
        }

        // Return a newly built handler
        return $this->app->make(MemcachedSessionHandler::class, [
            $memcached,
            ['prefix' => array_get($config, 'name') ?: 'CCM_SESSION']
        ]);
    }

    /**
     * Return the default session handler
     *
     * @param array $config The `concrete.session` config item
     *
     * @return \Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler
     */
    protected function getDefaultHandler(array $config)
    {
        return $this->getFileHandler($config);
    }

    /**
     * Get a session storage object based on configuration
     *
     * @param array $config
     *
     * @return \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    private function getSessionStorage(array $config)
    {
        $app = $this->app;

        // If we're running through command line, just early return an in-memory storage
        if ($app->isRunThroughCommandLineInterface()) {
            return $app->make(MockArraySessionStorage::class);
        }

        // Resolve the handler based on config
        $handler = $this->getSessionHandler($config);
        $storage = $app->make(NativeSessionStorage::class, [[], $handler]);

        // Initialize the storage with some options
        $options = array_get($config, 'cookie', []);
        $options['gc_maxlifetime'] = array_get($config, 'max_lifetime');

        if (array_get($options, 'cookie_path', false) === false) {
            $options['cookie_path'] = $app['app_relative_path'] . '/';
        }

        $storage->setOptions($options);
        return $storage;
    }

    /**
     * Get a new session handler
     *
     * @param array $config The config from our config repository
     *
     * @return \SessionHandlerInterface
     *
     * @throws \RuntimeException When a configured handler does not exist
     */
    private function getSessionHandler(array $config)
    {
        $handler = array_get($config, 'handler', 'default');

        // Build handler using a matching method "get{Type}Handler"
        $method = Str::camel("get_{$handler}_handler");
        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        }

        /**
         * @todo Change this to return an exception if an unsupported handler is configured. This makes it easier to get
         * configuration dialed in properly
         */
        //throw new \RuntimeException(t('Unsupported session handler "%s"', $handler));

        // Return the default session handler by default
        return $this->getSessionHandler(['handler' => 'default'] + $config);
    }

    /**
     * Generator for only returning hosts that aren't already added to the memcache instance
     *
     * @param \Memcached $memcached
     * @param array $servers The servers as described in config
     *
     * @return \Generator|string[] [ $host, $port, $weight ]
     */
    private function newMemcachedServers(Memcached $memcached, array $servers)
    {
        $serverIndex = [];
        $existingServers = $memcached->getServerList();

        foreach ($existingServers as $server) {
            $serverIndex[$server['host'] . ':' . $server['port']] = true;
        }

        foreach ($servers as $configServer) {
            $server = [
                'host' => array_get($configServer, 'host', ''),
                'port' => array_get($configServer, 'port', 11211),
                'weight' => array_get($configServer, 'weight', 0),
            ];

            if (!isset($serverIndex[$server['host'] . ':' . $server['port']])) {
                yield $server;
            }
        }
    }

}
