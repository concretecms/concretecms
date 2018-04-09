<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Queue\RoundRobinQueue as BaseRoundRobinQueue;

class RoundRobinQueue extends BaseRoundRobinQueue
{

    public function getQueues()
    {
        return $this->queues;
    }

}