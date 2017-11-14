<?php
namespace Concrete\Core\Page;

use Concrete\Core\Entity\Calendar\CalendarEvent;

class DuplicateEventEvent extends Event
{
    protected $newEvent;

    public function setNewEventObject(CalendarEvent $newEvent)
    {
        $this->newEvent = $newEvent;
    }

    public function getNewEventObject()
    {
        return $this->newEvent;
    }
}
