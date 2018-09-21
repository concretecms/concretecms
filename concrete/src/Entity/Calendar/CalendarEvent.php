<?php
namespace Concrete\Core\Entity\Calendar;

use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Attribute\Category\EventCategory;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Entity\Attribute\Value\EventValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use RuntimeException;

/**
 * @ORM\Entity(repositoryClass="CalendarEventRepository")
 * @ORM\Table(name="CalendarEvents")
 */
class CalendarEvent implements ObjectInterface
{

    use ObjectTrait;

    /**
     * This points to the currently selected version in the object.
     * @var CalendarEventVersion
     */
    protected $selectedVersion;

    /**
     * @ORM\ManyToOne(targetEntity="Calendar", inversedBy="events")
     * @ORM\JoinColumn(name="caID", referencedColumnName="caID")
     */
    protected $calendar;

    /**
     * @ORM\OneToMany(targetEntity="CalendarEventVersion", mappedBy="event", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="eventVersionID", referencedColumnName="eventVersionID")
     */
    protected $versions;

    /**
     * @ORM\OneToMany(targetEntity="CalendarEventWorkflowProgress", mappedBy="event", cascade={"remove"})
     * @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     */
    protected $workflow_progress_objects;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $eventID;

    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
        $this->versions = new ArrayCollection();
    }

    /**
     * @return EventOccurrenceList
     */
    public function getOccurrenceList()
    {
        $ev = new EventOccurrenceList();
        $ev->filterByEvent($this);
        return $ev;
    }

    /**
     * @return CalendarEventOccurrence[]
     */
    public function getOccurrences()
    {
        $list = $this->getOccurrenceList();
        return $list->getResults();
    }

    /**
     * @return Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    public function getCalendarID()
    {
        if (isset($this->calendar)) {
            return $this->calendar->getID();
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->eventID;
    }

    /**
     * @return CalendarEventVersion
     */
    public function getVersions()
    {
        $criteria = Criteria::create();
        $criteria->orderBy(['evDateAdded' => Criteria::DESC]);
        return $this->versions->matching($criteria);
    }

    /**
     * @return CalendarEventVersion
     */
    public function getSelectedVersion()
    {
        return $this->selectedVersion;
    }

    /**
     * @param CalendarEventVersion $selectedVersion
     */
    public function setSelectedVersion($selectedVersion)
    {
        $this->selectedVersion = $selectedVersion;
    }

    /**
     * @return CalendarEventVersion
     */
    public function getApprovedVersion()
    {
        $criteria = Criteria::create()->setMaxResults(1);
        $expr = $criteria::expr();
        $criteria->where($expr->eq('evIsApproved', true));
        return $this->versions->matching($criteria)->first();
    }

    /**
     * @return CalendarEventVersion
     */
    public function getRecentVersion()
    {
        $criteria = Criteria::create()->setMaxResults(1);
        $criteria->orderBy(['evDateAdded' => Criteria::DESC]);
        return $this->versions->matching($criteria)->first();
    }

    public function __call($name, $arguments)
    {
        if (!isset($this->selectedVersion)) {
            $this->selectedVersion = $this->getApprovedVersion();
        }

        if (!$this->selectedVersion) {
            throw new RuntimeException(t('Unable to load selected version for event %s', $this->getID()));
        }

        if (!method_exists($this->selectedVersion, $name)) {
            throw new RuntimeException(t('Method %s does not exist for CalendarEventVersion class.', $name));
        }

        return call_user_func_array([$this->selectedVersion, $name], $arguments);
    }

    /**
     * @return EventCategory
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     */
    public function getObjectAttributeCategory()
    {
        $app = Facade::getFacadeApplication();
        return $app->make(EventCategory::class);
    }

    /**
     * @param string|\Concrete\Core\Entity\Attribute\Key\EventKey $ak
     * @param bool $createIfNotExists
     *
     * @return EventValue|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = EventKey::getByHandle($ak);
        }
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this->getSelectedVersion());
        } else {
            $value = null;
        }

        if ($value === null && $createIfNotExists) {
            $value = new EventValue();
            $value->setVersion($this->getSelectedVersion());
            $value->setAttributeKey($ak);
        }

        return $value;
    }

    /**
     * Returns true if none of the versions are approved
     */
    public function isPending()
    {
        $approved = $this->getApprovedVersion();
        if (!$approved) {
            return true;
        }
        return false;
    }
}