<?php

namespace Concrete\Core\Foundation\Queue;

use Concrete\Core\Support\Facade\Facade;

class Queue extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return QueueService::class;
    }
}
