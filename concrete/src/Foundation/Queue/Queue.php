<?php

namespace Concrete\Core\Foundation\Queue;

use Concrete\Core\Support\Facade\Facade;

/**
 * @deprecated Use $app->make(\Concrete\Core\Foundation\Queue\QueueService::class)
 */
class Queue extends Facade
{
    /**
     * @return string
     * @since 8.3.0
     */
    public static function getFacadeAccessor()
    {
        return QueueService::class;
    }
}
