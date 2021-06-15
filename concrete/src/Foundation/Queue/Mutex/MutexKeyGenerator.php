<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;
use Concrete\Core\Job\QueueableJob;

class MutexKeyGenerator
{

    public function getMutexKey($mixed)
    {
        if ($mixed instanceof QueueableJob) {
            $mixed = sprintf('job_%s', $mixed->getJobHandle());
        }
        $key = sprintf('queue_%s', (string) $mixed);
        return $key;
    }


}