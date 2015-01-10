<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Attribute\Value\EventValue;
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

                return $event;
            }
        }

        return null;
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
                    'repetitionID' => $this->getRepetition()->getID()
                ))
            ) {
                $this->id = intval($connection->lastInsertId(), 10);
                $this->generateOccurrences();
                return true;
            }
        }

        return false;
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

    protected function generateOccurrences()
    {

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

}
