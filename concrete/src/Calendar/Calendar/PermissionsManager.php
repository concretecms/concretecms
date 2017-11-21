<?php
namespace Concrete\Core\Calendar\Calendar;

use Concrete\Core\Permission\Key\Key;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Calendar\Calendar as CalendarEntity;
use Concrete\Core\Entity\Calendar\CalendarPermissionAssignment;

class PermissionsManager
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function clearCustomPermissions(CalendarEntity $calendar)
    {
        $em = $this->entityManager;

        $calendar->setOverridePermissions(false);

        $assignments = $em->getRepository(CalendarPermissionAssignment::class)->findByCalendar($calendar);
        foreach($assignments as $assignment) {
            $em->remove($assignment);
        }
        $em->persist($calendar);
        $em->flush();
    }

    public function setPermissionsToOverride(CalendarEntity $calendar)
    {
        if (!$calendar->arePermissionsSetToOverride()) {
            $this->clearCustomPermissions($calendar);

            $em = $this->entityManager;

            $permissions = Key::getList('calendar');
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($calendar);
                $paID = $pk->getPermissionAccessID();
                if ($paID) {
                    $assignment = new CalendarPermissionAssignment();
                    $assignment->setPermissionAccessID($paID);
                    $assignment->setPermissionKeyID($pk->getPermissionKeyID());
                    $assignment->setCalendar($calendar);
                    $em->persist($assignment);
                }
            }

            $calendar->setOverridePermissions(true);
            $em->persist($calendar);
            $em->flush();
        }
    }

}
