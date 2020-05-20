<?php

namespace Concrete\Core\Calendar\Event\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class CalendarEventCommand implements CommandInterface
{

    protected $eventID;

    /**
     * @param $eventID
     */
    public function __construct($eventID)
    {
        $this->eventID = $eventID;
    }

    /**
     * @return mixed
     */
    public function getEventID()
    {
        return $this->eventID;
    }



}
