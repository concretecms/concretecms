<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Driver;
use Bernard\QueueFactory\PersistentFactory;
use Concrete\Core\Foundation\Queue\Serializer\Serializer;
/**
 * We require our own factory because bernard's hard-codes its own crappy serializer.
 * Class QueueFactory
 * @package Concrete\Core\Foundation\Queue
 */
class QueueFactory extends PersistentFactory
{

    /**
     * @param Driver     $driver
     * @param Serializer $serializer
     */
    public function __construct(Driver $driver, Serializer $serializer)
    {
        $this->queues = [];
        $this->driver = $driver;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function create($queueName)
    {
        if (isset($this->queues[$queueName])) {
            return $this->queues[$queueName];
        }

        $queue = new PersistentQueue($queueName, $this->driver, $this->serializer);

        return $this->queues[$queueName] = $queue;
    }


}