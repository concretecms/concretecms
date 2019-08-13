<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Support\Facade\Facade;

/**
 * @since 8.3.0
 */
class EventOccurrence extends Facade
{

    public static function getFacadeAccessor()
    {
        return EventOccurrenceService::class;
    }

}
