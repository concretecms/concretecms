<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;
use Concrete\Core\Foundation\Queue\RoundRobinQueue;

class RoundRobinQueueMutexGenerator extends AbstractMutexGenerator
{

    /**
     * @param RoundRobinQueue $queue
     * @param $callback
     */
    public function execute(Queue $queue, callable $callback)
    {
        $keys = [];
        foreach($queue->getQueues() as $singleQueue)
        {
            $key = $this->keyGenerator->getMutexKey($singleQueue);
            $keys[] = $key;
            $this->mutexer->acquire($key);
        }

        try {
            $callback();
        } finally {
            foreach($keys as $key) {
                $this->mutexer->release($key);
            }
        }

    }
}