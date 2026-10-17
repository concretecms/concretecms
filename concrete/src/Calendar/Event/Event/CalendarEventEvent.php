<?php
namespace Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Symfony\Component\EventDispatcher\GenericEvent;

/** Generic wrapper dispatched with all calendar event service events.  Subject is the affected entity
 *  (CalendarEvent or CalendarEventVersion); entityManager is provided so listeners need not re-derive it. */
class CalendarEventEvent extends GenericEvent
{
    public function getEntityManager()
    {
        return $this->getArgument('entityManager');
    }
    public function getEventObject(): ?CalendarEvent
    {
        $s=$this->getSubject();
        return ($s instanceof CalendarEvent)?$s:null;
    }
    public function getEventVersionObject(): ?CalendarEventVersion
    {
        $s=$this->getSubject();
        return ($s instanceof CalendarEventVersion)?$s:null;
    }
}