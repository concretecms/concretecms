<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Attribute\Value\EventValue;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Foundation\Repetition\RepetitionInterface;

/**
 * Generic Event class
 *
 * @package Concrete\Core\Calendar
 */
class Event implements EventInterface
{

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var RepetitionInterface */
    protected $repetition;

    /** @var \Concrete\Core\Calendar\Calendar */
    protected $calendar;

    /**
     * @param string              $name
     * @param string              $description
     * @param RepetitionInterface $repetition
     */
    function __construct($name, $description, RepetitionInterface $repetition)
    {
        $this->name = $name;
        $this->description = $description;
        $this->repetition = $repetition;
    }

    /**
     * @param $id
     * @return Event|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getByID($id)
    {
        $id = intval($id, 10);

        $connection = \Database::connection();
        $query = $connection->query('SELECT * FROM CalendarEvents WHERE eventID=' . $id);
        foreach ($query as $result) {
            if (intval(array_get($result, 'eventID')) === $id) {
                $repetition = EventRepetition::getByID(array_get($result, 'repetitionID', null));
                $event = new Event(
                    array_get($result, 'name'),
                    array_get($result, 'description'),
                    $repetition);
                $event->id = $id;
                $calendar = Calendar::getByID($result['caID']);
                if (is_object($calendar)) {
                    $event->setCalendar($calendar);
                }
                return $event;
            }
        }

        return null;
    }

    /**
     * return |Concrete\Core\Calendar\EventOccurrenceList
     */
    public function getOccurrenceList()
    {
        $ev = new EventOccurrenceList();
        $ev->filterByEvent($this);
        return $ev;
    }

    /**
     * return \Concrete\Core\Calendar\EventOccurrence[]
     */
    public function getOccurrences()
    {
        $list = $this->getOccurrenceList();
        return $list->getResults();
    }

    /**
     * @return bool
     */
    public function save()
    {
        $connection = \Database::connection();
        if ($this->id) {
            if ($connection->update(
                'CalendarEvents',
                array(
                    'name'         => $this->getName(),
                    'description'  => $this->getDescription(),
                    'caID'         => $this->getCalendarID(),
                    'repetitionID' => $this->getRepetition()->getID()
                ),
                array(
                    'eventID' => $this->getID()
                ))
            ) {
                return true;
            }
        } else {
            if ($connection->insert(
                'CalendarEvents',
                array(
                    'name'         => $this->getName(),
                    'description'  => $this->getDescription(),
                    'caID'         => $this->getCalendarID(),
                    'repetitionID' => $this->getRepetition()->getID()
                ))
            ) {
                $this->id = intval($connection->lastInsertId(), 10);

                /** @var EventOccurrenceFactory $factory */
                $factory = \Core::make('calendar/event/occurrence/factory');
                $start_time = time();
                $end_time = strtotime('+5 years', $start_time);
                $occurrences = $this->getRepetition()->activeRangesBetween($start_time, $end_time);

                $initial_occurrence = $factory->createEventOccurrence(
                    $this,
                    strtotime($this->repetition->getStartDate()),
                    strtotime($this->repetition->getEndDate()));
                $initial_occurrence->save();

                foreach ($occurrences as $occurrence) {
                    if ($occurrence[0] === $initial_occurrence->getStart()) {
                        continue;
                    }
                    $factory->createEventOccurrence($this, $occurrence[0], $occurrence[1])->save();
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Reindex the attributes on this Event.
     * @return void
     */
    public function reindex()
    {
        $attribs = EventKey::getAttributes(
            $this->getID(),
            'getSearchIndexValue'
        );
        $db = \Database::connection();

        $db->Execute('delete from CalendarEventSearchIndexAttributes where eventID = ?', array($this->getID()));
        $searchableAttributes = array('eventID' => $this->getID());

        $key = new EventKey();
        $key->reindex('CalendarEventSearchIndexAttributes', $searchableAttributes, $attribs);
    }

    public function delete()
    {
        if ($this->getID() > 0) {
            $db = \Database::connection();
            if ($db->delete('CalendarEvents', array('eventID' => intval($this->getID())))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \Concrete\Core\Calendar\Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param \Concrete\Core\Calendar\Calendar $calendar
     */
    public function setCalendar(\Concrete\Core\Calendar\Calendar $calendar)
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return RepetitionInterface
     */
    public function getRepetition()
    {
        return $this->repetition;
    }

    public function setRepetition(RepetitionInterface $repetition)
    {
        $this->repetition = $repetition;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Helper method for retrieving attribute values against this event object
     *
     * @param EventKey $key
     * @param bool     $create_on_miss
     * @return EventValue|null
     */
    public function getAttributeValueObject(EventKey $key, $create_on_miss = false)
    {
        return EventValue::getAttributeValueObject($this, $key, !!$create_on_miss);
    }

    /**
     * @return \stdClass
     */
    public function getJSONObject()
    {
        $o = new \stdClass;
        $o->id = $this->getID();
        $o->name = $this->getName();
        $o->description = $this->getDescription();
        return $o;
    }
}
