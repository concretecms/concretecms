<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;

class QueueMutexGenerator extends AbstractMutexGenerator
{

    public function execute(Queue $queue, callable $callback)
    {
        $key = $this->keyGenerator->getMutexKey($queue);
        $this->mutexer->execute($key, $callback);
    }

}