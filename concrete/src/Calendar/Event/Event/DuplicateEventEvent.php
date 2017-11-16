<?php
namespace Concrete\Core\Calendar\Event\Event;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

class DuplicateEventEvent extends GenericEvent
{
    protected $newEvent;
    protected $entityManager;

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param CalendarEvent $newEvent
     */
    public function setNewEventObject(CalendarEvent $newEvent)
    {
        $this->newEvent = $newEvent;
    }

    /**
     * @return CalendarEvent
     */
    public function getNewEventObject()
    {
        return $this->newEvent;
    }
}
