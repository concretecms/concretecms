<?php
namespace Concrete\Core\Calendar\Event\Workflow\Progress;

use Concrete\Core\Workflow\Progress\CalendarEventProgress;

class Event
{
    protected $event;
    protected $progress;

    public function __construct(\Concrete\Core\Entity\Calendar\CalendarEvent $event, CalendarEventProgress $progress)
    {
        $this->event = $event;
        $this->progress = $progress;
    }

    public function getEventObject()
    {
        return $this->event;
    }
    public function getWorkflowProgressObject()
    {
        return $this->progress;
    }
}
