<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;

class MutexKeyGenerator
{

    public function getMutexKey($mixed)
    {
        $key = sprintf('queue_%s', (string) $mixed);
        return $key;
    }


}