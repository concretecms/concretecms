<?php

namespace Concrete\Core\Calendar\Event\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class CalendarEventCommand extends Command
{
    /**
     * @var int
     */
    protected $eventID;

    public function __construct(int $eventID)
    {
        $this->eventID = $eventID;
    }

    public function getEventID(): int
    {
        return $this->eventID;
    }
}
