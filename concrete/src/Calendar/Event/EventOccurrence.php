<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Support\Facade\Facade;

class EventOccurrence extends Facade
{

    public static function getFacadeAccessor()
    {
        return EventOccurrenceService::class;
    }

}
