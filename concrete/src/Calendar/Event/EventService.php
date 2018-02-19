<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Repetition\Comparator;
use Concrete\Core\Calendar\Event\Event\DuplicateEventEvent;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\User\User;
use Concrete\Core\Attribute\Category\EventCategory;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Calendar\CalendarRelatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventService
{
    protected $entityManager;
    protected $config;
    protected $occurrenceFactory;
    protected $eventCategory;
    protected $dispatcher;

    const EVENT_VERSION_RECENT = 1;
    const EVENT_VERSION_APPROVED = 2;
    const INTERVAL_VERSION = 1200; // 20 minutes

    public function __construct(EntityManagerInterface $entityManagerInterface, Repository $config, EventOccurrenceFactory $occurrenceFactory, EventCategory $eventCategory, EventDispatcher $dispatcher)
    {
        $this->entityManager = $entityManagerInterface;
        $this->config = $config;
        $this->occurrenceFactory = $occurrenceFactory;
        $this->eventCategory = $eventCategory;
        $this->dispatcher = $dispatcher;
    }

    public function getByID($id, $retrieveVersion = self::EVENT_VERSION_APPROVED)
    {
        $r = $this->entityManager->getRepository(CalendarEvent::class);
        $event = $r->findOneById($id);
        if ($event) {
            switch($retrieveVersion) {
                case self::EVENT_VERSION_APPROVED:
                    $v = $event->getApprovedVersion();
                    break;

                default:
                    $v = $event->getRecentVersion();
                    break;
            }
        }
        if (isset($v)) {
            $event->setSelectedVersion($v);
        }
        return $event;
    }

    public function getVersionByID($id)
    {
        $r = $this->entityManager->getRepository(CalendarEventVersion::class);
        return $r->findOneByEventVersionID($id);
    }

    /**
     * Returns a new calendar event version object, duplicated from the most recent one –
     * or if the event has no version objects simply returns a new one.
     * @param CalendarEvent $event
     */
    public function getVersionToModify(CalendarEvent $event, User $u)
    {
        if (!count($event->getVersions())) {
            return new CalendarEventVersion($event, $u->getUserInfoObject()->getEntityObject());
        }

        $recent = $event->getRecentVersion();

        $now = new \DateTime('now', new \DateTimeZone($event->getCalendar()->getTimezone()));
        $recentVersionDate = $recent->getDateAdded();
        if ($recent->getAuthor()->getUserID() == $u->getUserID() && !$recent->isApproved() && (
            ($now->getTimestamp() - $recentVersionDate->getTimestamp()) < self::INTERVAL_VERSION
            )) {
            // We can use the same version.
            return $recent;
        } else if ($recent) {
            $return = clone $recent;
            $return->setAuthor($u->getUserInfoObject()->getEntityObject());
            $return->setIsApproved(false);

            // Persist the cloned version
            $this->entityManager->persist($return);
            $this->entityManager->flush();

            // Duplicate attribute Values
            $values = $this->eventCategory->getAttributeValues($recent);
            foreach($values as $value) {
                $value = clone $value;
                $value->setVersion($return);
                $this->entityManager->persist($value);
            }
            $this->entityManager->flush();

            return $return;
        }
    }

    public function addEventVersion(CalendarEvent $event, Calendar $calendar, CalendarEventVersion $version, $repetitions = array())
    {
        if (count($repetitions)) {
            // We are providing repetiion objects. That means we need to delete the existing repetition objects on the
            // provided version.
            if (count($version->getRepetitionEntityCollection())) {
                foreach($version->getRepetitionEntityCollection() as $repetition) {
                    $this->entityManager->remove($repetition);
                }
                $this->entityManager->flush();
            }
            $version->setRepetitions($repetitions);
        }

        $this->entityManager->persist($version);
        $event->getVersions()->add($version);
        $event->setCalendar($calendar);
        $version->setEvent($event);
        $event = $this->save($event);
    }

    public function save(CalendarEvent $event)
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
        return $event;
    }

    public function approve(CalendarEventVersion $version)
    {
        $currentlyApproved = $version->getEvent()->getApprovedVersion();
        if ($currentlyApproved) {
            $currentlyApproved->setIsApproved(false);
            $currentlyApproved->setDateActivated(null);
            $this->entityManager->persist($version);
        }

        $event = $version->getEvent();
        $calendar = $event->getCalendar();

        $version->setIsApproved(true);
        $version->setDateActivated(new \DateTime('now', new \DateTimeZone($calendar->getTimezone())));
        $this->entityManager->flush();

        $enableMoreDetails = $calendar->enableMoreDetails();

        if ($enableMoreDetails == 'C' && !$event->getPageID()) {
            // we haven't created a page yet. So let's make one.
            $parent = Page::getByID($calendar->getEventPageParentID());
            if (is_object($parent) && !$parent->isError()) {
                $type = Type::getByID($calendar->getEventPageTypeID());
                if (is_object($type)) {
                    $page = $parent->add($type, array('cName' => $event->getName()));
                    $page->setAttribute($calendar->getEventPageAttributeKeyHandle(), $event);
                    $event->setPageID($page->getCollectionID());
                    $event->setRelatedPageRelationType('C');
                    $this->save($event);
                }
            }
        }
    }

    public function unapprove(CalendarEvent $event)
    {
        $event->setIsApproved(false);
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function duplicate(CalendarEvent $event, User $u, Calendar $calendar = null)
    {
        $values = $this->eventCategory->getAttributeValues($event->getRecentVersion());

        $calendar = $calendar ? $calendar : $event->getCalendar();

        $new = new CalendarEvent($calendar);
        $version = clone $event->getRecentVersion();
        $version->setDateActivated(null);
        $version->setAuthor($u->getUserInfoObject()->getEntityObject());
        $version->setIsApproved(false);
        $version->setName($version->getName() . ' ' . t('Copy'));
        $new->getVersions()->add($version);
        $version->setEvent($new);

        $this->entityManager->persist($version);
        $this->entityManager->persist($new);
        $this->entityManager->flush();

        // Duplicate attribute Values
        foreach($values as $value) {
            $value = clone $value;
            $value->setVersion($version);
            $this->entityManager->persist($value);
        }
        $this->entityManager->flush();
        
        $this->generateDefaultOccurrences($version);

        $duplicateEvent = new DuplicateEventEvent($this);
        $duplicateEvent->setEntityManager($this->entityManager);
        $duplicateEvent->setNewEventObject($new);
        $this->dispatcher->dispatch('on_calendar_event_duplicate', $duplicateEvent);

        return $new;
    }

    public function delete(CalendarEvent $event)
    {
        if ($event->getPageID() && $event->getRelatedPageRelationType() == 'C') {
            $calendarPage = Page::getByID($this->cID);
            if ($calendarPage && !$calendarPage->isError()) {
                if ($this->config->get('concrete.misc.enable_trash_can')) {
                    $calendarPage->moveToTrash();
                } else {
                    $calendarPage->delete();
                }
            }
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }

    public function deleteVersion(CalendarEventVersion $version)
    {
        $this->entityManager->remove($version);
        $this->entityManager->flush();
    }

    public function isRelatedTo(CalendarEvent $event1, CalendarEvent $event2)
    {
        $r = $this->entityManager->getRepository(CalendarRelatedEvent::class);
        $relation1 = $r->findOneByEvent($event1);
        $relation2 = $r->findOneByEvent($event2);
        if ($relation1 && $relation2 && $relation1->getRelationID() == $relation2->getRelationID()) {
            return true;
        }
        return false;
    }

    /**
     * Handles generating occurrences with the default start and end times
     */
    public function generateDefaultOccurrences(CalendarEventVersion $version)
    {
        $repetitions = $version->getRepetitionEntityCollection();
        $query = $this->entityManager->createQuery('delete from calendar:CalendarEventVersionOccurrence o where o.version = :version');
        $query->setParameter('version', $version);
        $query->execute();

        foreach($repetitions as $repetitionEntity) {

            $repetition = $repetitionEntity->getRepetitionObject();

            $start = $repetition->getStartDateTimestamp() - 1;

            $datetime = new \DateTime('+5 years', $repetition->getTimezone());
            $end = $datetime->getTimestamp();

            $this->occurrenceFactory->generateOccurrences($version, $repetitionEntity, $start, $end);
        }
    }

    /**
     * Returns true if the difference between the event versions impacts repetitions and occurrences.
     */
    public function requireOccurrenceRegeneration($repetitions1, $repetitions2)
    {
        if (count($repetitions1) != count($repetitions2)) {
            return true;
        }

        $comparator = new Comparator();

        for ($i = 0; $i < count($repetitions1); $i++) {
            if (!$comparator->areEqual($repetitions1[$i]->getRepetitionObject(), $repetitions2[$i]->getRepetitionObject())) {
                return true;
            }
        }

        return false;
    }


}
