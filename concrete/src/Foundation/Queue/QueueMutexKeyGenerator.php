<?php

namespace Concrete\Core\Foundation\Queue;

class QueueMutexKeyGenerator
{

    public function getMutexKey($mixed)
    {
        $queue = (string) $mixed;
        return sprintf('queue_%s', $queue);
    }

}