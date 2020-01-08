<?php

namespace Concrete\Core\Events\Broadcast\Driver;

use Predis\ClientInterface;

class RedisDriver implements DriverInterface
{

    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function broadcast($channel, $message)
    {
        $this->client->publish($channel, $message);
    }


}