<?php

namespace Concrete\Core\Calendar\Event\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class CalendarEventCommand implements CommandInterface
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
