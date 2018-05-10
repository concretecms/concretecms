<?php
namespace Concrete\Core\Calendar\Event;

use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Calendar\CalendarEventOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;

class EventOccurrenceService
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    /**
     * @param $id
     * @return CalendarEventVersionOccurrence
     */
    public function getByID($versionOccurrenceID)
    {
        $r = $this->entityManager->getRepository('calendar:CalendarEventVersionOccurrence');
        return $r->findOneBy(['versionOccurrenceID' => $versionOccurrenceID]);
    }

    /**
     * This code below retrieves based on the occurrence ID which can remain the same across versions.
     */
    public function getByOccurrenceID($occurrenceID, $retrieveVersion = EventService::EVENT_VERSION_APPROVED)
    {
        if ($retrieveVersion == EventService::EVENT_VERSION_RECENT) {
            $query = $this->entityManager->createQuery('select vo from calendar:CalendarEventVersionOccurrence vo JOIN
    vo.version v JOIN vo.occurrence o where o.occurrenceID = :occurrenceID order by v.evDateAdded DESC');
        } else {
            $query = $this->entityManager->createQuery('select vo from calendar:CalendarEventVersionOccurrence vo JOIN
    vo.version v JOIN vo.occurrence o where o.occurrenceID = :occurrenceID and v.evIsApproved = 1');
        }
        $query->setMaxResults(1);
        $query->setParameter('occurrenceID', $occurrenceID);
        $object = $query->getOneOrNullResult();
        return $object;
    }

    public function save(CalendarEventVersionOccurrence $occurrence)
    {
        $this->entityManager->persist($occurrence);
        $this->entityManager->flush();
        return $occurrence;
    }

    public function delete(CalendarEventVersion $version, CalendarEventOccurrence $occurrence)
    {
        $this->entityManager->refresh($version);
        foreach($version->getOccurrences() as $versionOccurrence) {
            if ($versionOccurrence->getOccurrence()->getID() == $occurrence->getID()) {
                $this->entityManager->remove($versionOccurrence);
            }
        }
        $this->entityManager->flush();
    }

    public function cancel(CalendarEventVersionOccurrence $occurrence)
    {
        $occurrence->setCancelled(true);
        $this->entityManager->persist($occurrence);
        $this->entityManager->flush();
        return $occurrence;
    }




}
