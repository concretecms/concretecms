<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarPermissionAssignments", indexes={
 * @ORM\Index(name="paID", columns={"paID"}),
 * @ORM\Index(name="pkID", columns={"pkID"})
 * })
 */
class CalendarPermissionAssignment
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Calendar", inversedBy="permission_assignments")
     * @ORM\JoinColumn(name="caID", referencedColumnName="caID")
     */
    protected $calendar;

    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $pkID;


    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $paID;

    /**
     * @return mixed
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param mixed $calendar
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return mixed
     */
    public function getPermissionKeyID()
    {
        return $this->pkID;
    }

    /**
     * @param mixed $pkID
     */
    public function setPermissionKeyID($pkID)
    {
        $this->pkID = $pkID;
    }

    /**
     * @return mixed
     */
    public function getPermissionAccessID()
    {
        return $this->paID;
    }

    /**
     * @param mixed $paID
     */
    public function setPermissionAccessID($paID)
    {
        $this->paID = $paID;
    }






}
