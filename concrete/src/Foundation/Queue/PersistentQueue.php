<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Driver;
use Bernard\Queue\AbstractQueue;
use Bernard\Queue\PersistentQueue as BernardPersistentQueue;
use Concrete\Core\Foundation\Queue\Serializer\Serializer;

class PersistentQueue extends BernardPersistentQueue
{

    /**
     * @param string     $name
     * @param Driver     $driver
     * @param Serializer $serializer
     */
    public function __construct($name, Driver $driver, Serializer $serializer)
    {
        AbstractQueue::__construct($name);

        $this->driver = $driver;
        $this->serializer = $serializer;
        $this->receipts = new \SplObjectStorage();

        $this->register();
    }
}