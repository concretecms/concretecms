<?php

namespace Concrete\Core\Foundation\Queue\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Bernard\Driver\PredisDriver;
use Predis\Client;

class DriverFactory
{

    protected $config;

    protected $app;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function createDriver()
    {
        $queue = $this->config->get('concrete.queue');
        if ($queue['driver'] == 'redis') {
            $client = $this->app->make('redis');
            return new PredisDriver($client);
        } else {
            $connection = $this->app->make(Connection::class);
            return new ConcreteDatabaseDriver($connection);
        }
    }
}
