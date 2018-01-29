<?php

namespace Concrete\Core\Events\Broadcast;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Events\Broadcast\Driver\DriverInterface;
use Concrete\Core\Events\Broadcast\Driver\RedisDriver;

final class Broadcaster
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var DriverInterface
     */
    protected $driver;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    protected function getDriver()
    {
        if (!isset($this->driver)) {
            $broadcast = $this->config->get('concrete.events.broadcast');
            if ($broadcast['driver'] == 'redis') {
                $client = $this->app->make('redis');
                $this->driver = new RedisDriver($client);
            } else {
                throw new \RuntimeException(t('Attempted to broadcast events but no valid broadcaster is created.'));
            }
        }
        return $this->driver;
    }

    public function broadcast($channel, $object)
    {
        $this->getDriver()->broadcast($channel, json_encode($object));
    }
}