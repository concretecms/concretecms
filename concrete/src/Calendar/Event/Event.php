<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Support\Facade\Facade;

/**
 * @since 8.3.0
 */
class Event extends Facade
{

    public static function getFacadeAccessor()
    {
        return EventService::class;
    }

}
