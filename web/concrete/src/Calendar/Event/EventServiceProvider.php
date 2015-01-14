<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Foundation\Service\Provider;

class EventServiceProvider extends Provider
{

    public function register()
    {
        \Core::bind('calendar/event/occurrence/factory', '\\Concrete\\Core\\Calendar\\Event\\EventOccurrenceFactory');
    }

}
