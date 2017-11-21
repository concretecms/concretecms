<?php
namespace Concrete\Core\Entity\Calendar;

use Concrete\Core\Foundation\Repetition\RepetitionInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarEventRepetitions")
 */
class CalendarEventRepetition
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $repetitionID;

    /**
     * @ORM\Column(type="object")
     */
    protected $repetitionObject;

    /**
     * @param $repetitionObject
     */
    public function __construct(RepetitionInterface $repetitionObject)
    {
        $this->repetitionObject = $repetitionObject;
    }

    public function getID()
    {
        return $this->repetitionID;
    }

    public function setID($repetitionID)
    {
        $this->repetitionID = $repetitionID;
    }

    /**
     * @return mixed
     */
    public function getRepetitionObject()
    {
        $repetition = $this->repetitionObject;
        $repetition->setID($this->getID());
        return $repetition;
    }

    /**
     * @param mixed $repetitionObject
     */
    public function setRepetitionObject($repetitionObject)
    {
        $this->repetitionObject = $repetitionObject;
        if ($this->getID()) {
            $this->repetitionObject->setID($this->getID());
        }
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->repetitionObject, $name], $arguments);
    }


}