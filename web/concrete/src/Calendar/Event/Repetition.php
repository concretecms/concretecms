<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Foundation\Repetition\AbstractRepetition;

/**
 * Calendar Event repetition
 *
 * @package Concrete\Core\Calendar
 */
class Repetition extends AbstractRepetition
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
        $query = \Database::connection()->executeQuery(
            'SELECT * FROM CalendarEventRepetitions WHERE repetitionID = ' . $id);

        foreach ($query as $row) {
            if ($serialized = array_get($row, 'repetitionObject')) {
                $object = unserialize($serialized);
                $object->repetitionID = $id;
                return $object;
            }
            break;
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

            $this->repetitionID = $id;
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

    public function getID()
    {
        return $this->repetitionID;
    }

}
