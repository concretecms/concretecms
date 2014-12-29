<?php
namespace Concrete\Core\Calendar\Event;

/**
 * Simple occurrence of an event
 *
 * @package Concrete\Core\Calendar\Event
 */
class EventOccurrence
{

    /** @var EventInterface */
    protected $event;

    /** @var int The time of this occurrence */
    protected $now;

    public function __construct(EventInterface $event, $now = null)
    {
        $this->now = $now;
        $this->event = $event;
    }

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    public function isActive()
    {
        return $this->event->getRepetition()->isActive($this->now);
    }

}
