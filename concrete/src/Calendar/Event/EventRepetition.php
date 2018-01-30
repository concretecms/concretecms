<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Foundation\Repetition\AbstractRepetition;

class EventRepetition extends AbstractRepetition
{

    protected $repetitionID;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->repetitionID;
    }

    /**
     * @param mixed $repetitionID
     */
    public function setID($repetitionID)
    {
        $this->repetitionID = $repetitionID;
    }

}
