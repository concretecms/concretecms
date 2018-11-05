<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Support\Facade\Facade;

class Event extends Facade
{

    public static function getFacadeAccessor()
    {
        return EventService::class;
    }

}
