<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Attribute\Value\EventValue;

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

    /** @var int */
    protected $repetition_id;

    /** @var Repetition */
    protected $repetition = false;

    /**
     * @param string         $name
     * @param string         $description
     * @param Repetition|int $repetition Either the repetition object or the id of the repetition object
     */
    function __construct($name, $description, $repetition = null)
    {
        $this->name = $name;
        $this->description = $description;

        if ($repetition instanceof Repetition) {
            $this->repetition = $repetition;
            $this->repetition_id = $repetition->getID();
        } else {
            $this->repetition_id = intval($repetition, 10);
        }
    }

    public static function getByID($id)
    {
        $id = intval($id, 10);

        $connection = \Database::connection();
        $query = $connection->query('SELECT * FROM CalendarEvents WHERE eventID=' . $id);
        foreach ($query as $result) {
            if (intval(array_get($result, 'eventID')) === $id) {
                $event = new Event(
                    array_get($result, 'name'),
                    array_get($result, 'description'),
                    array_get($result, 'repetitionID'));
                $event->id = $id;

                return $event;
            }
        }

        return null;
    }

    public function save()
    {
        $connection = \Database::connection();
        if ($this->id) {
            $connection->update(
                'CalendarEvents',
                array(
                    'name'         => $this->getName(),
                    'description'  => $this->getDescription(),
                    'repetitionID' => $this->getRepetition()->getID()
                ),
                array(
                    'eventID' => $this->getID()
                ));
        } else {
            $connection->insert(
                'CalendarEvents',
                array(
                    'name'         => $this->getName(),
                    'description'  => $this->getDescription(),
                    'repetitionID' => $this->getRepetition()->getID()
                ));
            $this->id = intval($connection->lastInsertId(), 10);
        }

        return true;
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
     * @return Repetition
     */
    public function getRepetition()
    {
        if ($this->repetition === false) {
            $this->repetition = Repetition::getByID($this->repetition_id);
        }

        return $this->repetition;
    }

    public function setRepetition(Repetition $repetition)
    {
        $this->repetition = $repetition;
        $this->repetition_id = intval($repetition->getID());
    }

    /**
     * @return int
     */
    public function getId()
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

}
