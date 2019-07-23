<?php
namespace Concrete\Core\Entity\Calendar;

use Concrete\Core\Attribute\ObjectInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarEventVersionOccurrences")
 */
class CalendarEventVersionOccurrence implements ObjectInterface
{

    public function getAttribute($ak, $mode = false)
    {
        return $this->version->getAttribute($ak, $mode);
    }

    public function getAttributeValue($ak)
    {
        return $this->version->getAttributeValue($ak);
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        return $this->version->getAttributeValueObject($ak, $createIfNotExists);
    }

    public function getObjectAttributeCategory()
    {
        return $this->version->getObjectAttributeCategory();
    }

    public function clearAttribute($ak)
    {
        $this->version->clearAttribute($ak);
    }

    public function setAttribute($ak, $value)
    {
        $this->version->setAttribute($ak, $value);
    }

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $versionOccurrenceID;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEventVersion", inversedBy="occurrences")
     * @ORM\JoinColumn(name="eventVersionID", referencedColumnName="eventVersionID")
     */
    protected $version;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEventOccurrence", cascade={"persist"})
     * @ORM\JoinColumn(name="occurrenceID", referencedColumnName="occurrenceID")
     */
    protected $occurrence;

    public function getJSONObject()
    {
        $ev = $this->getEvent();
        $r = array();
        $r['start'] = $this->occurrence->getStart();
        $r['end'] = $this->occurrence->getEnd();

        return (object) array_merge($r, (array) $ev->getJSONObject());
    }

    public function __construct(CalendarEventVersion $version, CalendarEventRepetition $repetition, $start, $end, $cancelled = false)
    {
        $this->version = $version;
        $this->occurrence = new CalendarEventOccurrence($repetition, $start, $end, $cancelled);
    }

    public function getEvent()
    {
        return $this->version->getEvent();
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return CalendarEventOccurrence
     */
    public function getOccurrence()
    {
        return $this->occurrence;
    }

    public function __clone()
    {
        if ($this->versionOccurrenceID) {
            $this->versionOccurrenceID = null;
        }
    }

    public function getID()
    {
        return $this->versionOccurrenceID;
    }

    public function __call($name, $arguments)
    {
        if (!method_exists($this->occurrence, $name)) {
            throw new \RuntimeException(t('Method %s does not exist for CalendarEventOccurrence class.', $name));
        }

        return call_user_func_array([$this->occurrence, $name], $arguments);
    }

}