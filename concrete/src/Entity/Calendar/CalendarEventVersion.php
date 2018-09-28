<?php
namespace Concrete\Core\Entity\Calendar;

use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Foundation\Repetition\RepetitionInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Attribute\Category\EventCategory;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Entity\Attribute\Value\EventValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\EntityListeners({"\Concrete\Core\Calendar\Event\Version\Listener"})
 * @ORM\Table(name="CalendarEventVersions")
 */
class CalendarEventVersion implements ObjectInterface, \JsonSerializable
{

    protected $categories;

    use ObjectTrait;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEvent", inversedBy="versions")
     * @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     */
    protected $event;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $author;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $eventVersionID;

    /**
     * @ORM\OneToMany(targetEntity="CalendarEventVersionRepetition", mappedBy="version", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="versionRepetitionID", referencedColumnName="versionRepetitionID")
     */
    protected $repetitions;

    /**
     * @ORM\OneToMany(targetEntity="CalendarEventVersionOccurrence", mappedBy="version", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="versionOccurrenceID", referencedColumnName="versionOccurrenceID")
     */
    protected $occurrences;

    /**
     * These have to be public so we can use doctrine criteria from another class. Sigh.
     * @ORM\Column(type="datetime")
     */
    public $evDateAdded;

    /**
     * These have to be public so we can use doctrine criteria from another class. Sigh.
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $evActivateDateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    public $evIsApproved = false;

    public function __construct(CalendarEvent $event, User $author)
    {
        $this->author = $author;
        $this->evDateAdded = new \DateTime('now', new \DateTimeZone($event->getCalendar()->getTimezone()));
        $this->evActivateDateTime = null;
        $this->repetitions = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $evDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $evName;

    /**
     * C = created page, A = associated page, null = don't enable
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $evRelatedPageRelationType;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $cID = 0;

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
        } else {
            $value = null;
        }

        if ($value === null && $createIfNotExists) {
            $value = new EventValue();
            $value->setVersion($this);
            $value->setAttributeKey($ak);
        }

        return $value;
    }


    /**
     * @return mixed
     */
    public function getRelatedPageRelationType()
    {
        return $this->evRelatedPageRelationType;
    }

    /**
     * @param mixed $relatedPageRelationType
     */
    public function setRelatedPageRelationType($relatedPageRelationType)
    {
        $this->evRelatedPageRelationType = $relatedPageRelationType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->evName;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->evName = $name;
    }

    /**
     * @return \Concrete\Core\Page\Page
     */
    public function getPageObject()
    {
        if ($this->cID) {
            $c = Page::getByID($this->cID, 'ACTIVE');
            if (is_object($c) && !$c->isError()) {
                return $c;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getPageID()
    {
        return $this->cID;
    }

    /**
     * @param string $name
     */
    public function setPageID($cID)
    {
        $this->cID = $cID;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->evIsApproved;
    }

    /**
     * @param bool $approved
     */
    public function setIsApproved($approved)
    {
        $this->evIsApproved = $approved;
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     */
    public function setPageObject(Page $page)
    {
        $this->cID = $page->getCollectionID();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->evDescription;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->evDescription = $description;
    }


    public function getID()
    {
        return $this->eventVersionID;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        $event = $this->event;
        $event->setSelectedVersion($this);
        return $event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        $o = array();
        $o['id'] = $this->event->getID();
        $o['name'] = $this->getName();
        $o['versionId'] = $this->getID();
        $o['description'] = $this->getDescription();

        return $o;
    }

    /**
     * @return mixed
     */
    public function getOccurrences()
    {
        return $this->occurrences;
    }


    public function getCategories()
    {
        if (!isset($this->categories)) {
            $key = $this->getEvent()->getCalendar()->getCalendarTopicsAttributeKey();
            if ($key) {
                $r = $this->getAttribute($key->getAttributeKeyHandle());
                if (is_object($r)) {
                    $this->categories = $r->getSelectedTopicNodes();
                } else {
                    $this->categories = $r;
                }
            } else {
                $this->categories = [];
            }
        }
        return $this->categories;
    }

    /**
     * @return RepetitionInterface[]
     */
    public function getRepetitions()
    {
        $repetitions = array();
        foreach ($this->repetitions as $r) {
            $repetitions[] = $r->getRepetitionObject();
        }
        return $repetitions;
    }

    public function getRepetitionEntityCollection()
    {
        return $this->repetitions;
    }

    public function setRepetitions($repetitions)
    {
        $this->repetitions = $repetitions;
    }

    /**
     * @return mixed
     */
    public function getDateAdded()
    {
        // This is so stupid.
        if ($this->evDateAdded) {
            return new \DateTime($this->evDateAdded->format("Y-m-d H:i:s"), new \DateTimeZone($this->getEvent()->getCalendar()->getTimezone()));
        }
    }

    /**
     * @return mixed
     */
    public function getDateActivated()
    {
        if ($this->evActivateDateTime) {
            return new \DateTime($this->evActivateDateTime->format("Y-m-d H:i:s"), new \DateTimeZone($this->getEvent()->getCalendar()->getTimezone()));
        }
    }

    /**
     * @param mixed $evActivateDateTime
     */
    public function setDateActivated($evActivateDateTime)
    {
        $this->evActivateDateTime = $evActivateDateTime;
    }

    public function __clone()
    {
        if ($this->eventVersionID) {
            $this->eventVersionID = null;
            $this->evDateAdded = new \DateTime('now',
                new \DateTimeZone($this->getEvent()->getCalendar()->getTimezone())
            );
            $repetitions = $this->repetitions;
            $occurrences = $this->occurrences;
            $this->repetitions = new ArrayCollection();
            $this->occurrences = new ArrayCollection();

            /**
             * @var $r CalendarEventVersionRepetition
             */
            foreach ($repetitions as $r) {
                $nr = clone $r;
                $nr->setVersion($this);
                $this->repetitions->add($nr);
            }

            /**
             * @var $o CalendarEventVersionOccurrence
             */
            foreach ($occurrences as $o) {
                $no = clone $o;
                $no->setVersion($this);
                $this->occurrences->add($no);
            }
        }
    }
}