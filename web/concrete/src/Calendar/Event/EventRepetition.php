<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Foundation\Repetition\AbstractRepetition;

/**
 * Calendar Event repetition
 *
 * @package Concrete\Core\Calendar
 */
class EventRepetition extends AbstractRepetition
{

    protected $repetitionID;

    /**
     * @param $id
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getByID($id)
    {
        $id = intval($id, 10);
        $row = \Database::connection()->executeQuery(
            'SELECT * FROM CalendarEventRepetitions WHERE repetitionID = ' . $id)->fetch();

        if ($serialized = array_get($row, 'repetitionObject')) {
            $object = unserialize($serialized);
            $object->repetitionID = intval($id, 10);
            return $object;
        }

        return null;
    }

    /**
     * @return bool Success or failure
     */
    public function save()
    {
        $connection = \Database::connection();
        if (!$this->getID()) {
            $connection->insert(
                'CalendarEventRepetitions',
                array(
                    'repetitionObject' => serialize($this)
                ));
            $id = $connection->lastInsertId();

            $this->repetitionID = intval($id, 10);
        } else {
            $connection->update(
                'CalendarEventRepetitions',
                array(
                    'repetitionObject' => serialize($this)
                ),
                array(
                    'repetitionID' => $this->getID()
                ));
        }

        return true;
    }

    public function delete()
    {
        if ($this->getID() > 0) {
            $db = \Database::connection();
            if ($db->delete('CalendarEventRepetitions', array('repetitionID' => intval($this->getID())))) {
                return true;
            }
        }
        return false;
    }

    public function getID()
    {
        return $this->repetitionID;
    }

}
