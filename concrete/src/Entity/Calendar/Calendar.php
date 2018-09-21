<?php
namespace Concrete\Core\Entity\Calendar;

use Concrete\Core\Calendar\Utility\Preferences;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Calendar\Calendar\PermissionsManager;

/**
 * @ORM\Entity(repositoryClass="CalendarRepository")
 * @ORM\Table(name="Calendars")
 */
class Calendar implements ObjectInterface, AssignableObjectInterface
{

    use AssignableObjectTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="CalendarEvent", mappedBy="calendar", cascade={"remove"})
     * @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     */
    protected $events;

    /**
     * @ORM\OneToMany(targetEntity="CalendarPermissionAssignment", mappedBy="calendar", cascade={"remove"})
     */
    protected $permission_assignments;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $caID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $caName;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $caOverridePermissions = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $eventPageAttributeKeyHandle;

    /**
     * C = create page, A = associate, null = don't enable
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $enableMoreDetails;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true, "default": 0})
     */
    protected $eventPageParentID = 0;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true, "default": 0})
     */
    protected $eventPageTypeID = 0;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true, "default": 0})
     */
    protected $eventPageAssociatedID = 0;

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->caID;
    }

    /**
     * @return mixed
     */
    public function arePermissionsSetToOverride()
    {
        return $this->caOverridePermissions;
    }

    /**
     * @param mixed $caOverridePermissions
     */
    public function setOverridePermissions($caOverridePermissions)
    {
        $this->caOverridePermissions = $caOverridePermissions;
    }

    public function getEventPageAttributeKeyHandle()
    {
        return $this->eventPageAttributeKeyHandle;
    }

    /**
     * @param mixed $eventPageAttributeKeyHandle
     */
    public function setEventPageAttributeKeyHandle($eventPageAttributeKeyHandle)
    {
        $this->eventPageAttributeKeyHandle = $eventPageAttributeKeyHandle;
    }

    public function getEventPageTypeID()
    {
        return $this->eventPageTypeID;
    }

    /**
     * @param mixed $eventPageTypeID
     */
    public function setEventPageTypeID($eventPageTypeID)
    {
        $this->eventPageTypeID = $eventPageTypeID;
    }

    public function getEventPageParentID()
    {
        return $this->eventPageParentID;
    }

    /**
     * @param mixed $eventPageParentID
     */
    public function setEventPageParentID($eventPageParentID)
    {
        $this->eventPageParentID = $eventPageParentID;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->caName;
    }

    /**
     * @param mixed $caName
     */
    public function setName($caName)
    {
        $this->caName = $caName;
    }

    public function getTimezone()
    {
        $site = $this->getSite();
        if ($site) {
            $config = $site->getConfigRepository();
            if ($config) {
                $timezone = $config->get('timezone');
            }
        }
        if (!isset($timezone) || !$timezone) {
            $timezone = date_default_timezone_get();
        }

        return $timezone;
    }

    public function getCalendarTopicsAttributeKey()
    {
        // eventually this might be overridden at the calendar level.
        $app = Facade::getFacadeApplication();
        $preferences = $app->make(Preferences::class);
        /**
         * @var $preferences Preferences
         */
        return $preferences->getCalendarTopicsAttributeKey();
    }

    /**
     * @return boolean
     */
    public function enableMoreDetails()
    {
        return $this->enableMoreDetails;
    }

    /**
     * @param boolean $enableMoreDetails
     */
    public function setEnableMoreDetails($enableMoreDetails)
    {
        $this->enableMoreDetails = $enableMoreDetails;
    }

    /**
     * @return mixed
     */
    public function getEventPageAssociatedID()
    {
        return $this->eventPageAssociatedID;
    }

    /**
     * @param mixed $eventPageAssociatedID
     */
    public function setEventPageAssociatedID($eventPageAssociatedID)
    {
        $this->eventPageAssociatedID = $eventPageAssociatedID;
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getID();
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\CalendarAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'calendar';
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\CalendarResponse';
    }

    public function setChildPermissionsToOverride()
    {
        return false;
    }

    public function setPermissionsToOverride()
    {
        /**
         * @var $manager PermissionsManager
         */
        $manager = \Core::make(PermissionsManager::class);
        return $manager->setPermissionsToOverride($this);
    }
}
