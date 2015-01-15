<?php
namespace Concrete\Core\Calendar\Event;

/**
 * Simple occurrence of an event
 *
 * @package Concrete\Core\Calendar\Event
 */
class EventOccurrence
{

    /** @var int */
    protected $id;

    /** @var EventInterface */
    protected $event;
    /** @var int The time this occurrence began */
    protected $start;
    /** @var int The time this occurrence is scheduled to end */
    protected $end;
    /** @var bool Is this cancelled? */
    protected $cancelled;

    /**
     * @param EventInterface $event
     * @param int            $start
     * @param int            $end
     * @param bool           $cancelled
     */
    public function __construct(EventInterface $event, $start, $end, $cancelled = false)
    {
        $this->event = $event;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Get a current / new EventOccurrence from an Event object
     * These may not exist in the database, so be sure to check `$occurrence->getID()`
     *
     * @param Event $event
     * @param int   $now The current timestamp
     * @return EventOccurrence|null
     */
    public static function getFromEvent(Event $event, $now = null)
    {
        if (!$now) {
            $now = time();
        }

        if ($range = $event->getRepetition()->getActiveRange($now)) {
            list($start, $end) = $range;

            $db = \Database::connection();
            $result = $db->query(
                'SELECT * FROM CalendarEventOccurrences WHERE eventID=? && startTime < ? && endTime > ?',
                array(
                    $event->getId(),
                    $now,
                    $now
                ))->fetch();

            if ($result) {
                $occurrence = new EventOccurrence(
                    $event,
                    array_get($result, 'startTime'),
                    array_get($result, 'endTime'),
                    !!array_get($result, 'cancelled'));

                $occurrence->id = intval(array_get($result, 'occurrenceID'));
            }

            return new EventOccurrence($event, $start, $end, false);
        }

        return null;
    }

    public static function getByID($id)
    {
        $db = \Database::get();
        $r = $db->GetRow('SELECT * FROM CalendarEventOccurrences WHERE occurrenceID = ?', array($id));
        if (is_array($r) && isset($r['occurrenceID'])) {
            $ev = Event::getByID($r['eventID']);
            if (is_object($ev)) {
                $o = new static($ev, $r['startTime'], $r['endDate'], $r['cancelled']);
                $o->id = $r['occurrenceID'];
                return $o;
            }
        }
    }

    public function cancel()
    {
        $this->setCancelled(true);
    }

    /**
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save()
    {
        $db = \Database::connection();
        if ($db->query(
            'INSERT INTO CalendarEventOccurrences (eventID, startTime, endTime, cancelled) VALUES (?, ?, ?, ?)',
            array(
                $this->getEvent()->getID(),
                $this->getStart(),
                $this->getEnd(),
                $this->isCancelled() ? 1 : 0
            ))
        ) {
            $this->id = $db->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param EventInterface $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param int $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param boolean $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
    }

    public function delete()
    {
        if ($this->getID() > 0) {
            $db = \Database::connection();
            if ($db->delete('CalendarEventOccurrences', array('occurrenceID' => intval($this->getID())))) {
                return true;
            }
        }
        return false;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getJSONObject()
    {
        $ev = $this->getEvent();
        $r = array();
        $r['start'] = $this->getStart();
        $r['end'] = $this->getEnd();
        return (object)array_merge($r, (array)$ev->getJSONObject());
    }

}
