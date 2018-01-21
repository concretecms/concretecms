<?php

namespace Concrete\Core\Events\Broadcast;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Events\Broadcast\Driver\RedisDriver;

class BroadcasterFactory
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
        $broadcast = $this->config->get('concrete.events.broadcast');
        if ($broadcast['driver'] == 'redis') {
            $client = $this->app->make('redis');
            return new RedisDriver($client);
        } else {
            throw new \RuntimeException(t('Attempted to broadcast events but no valid broadcaster is created.'));
        }
    }
}