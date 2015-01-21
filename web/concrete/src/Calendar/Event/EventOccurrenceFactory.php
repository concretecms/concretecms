<?php
namespace Concrete\Core\Calendar\Event;

class EventOccurrenceFactory
{

    /**
     * @param Event $event
     * @param       $start
     * @param       $end
     * @return EventOccurrence
     */
    public function createEventOccurrence(Event $event, $start, $end)
    {
        return new EventOccurrence($event, $start, $end);
    }

}
