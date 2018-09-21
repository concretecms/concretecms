<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarEventVersionRepetitions")
 */
class CalendarEventVersionRepetition
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $versionRepetitionID;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEventVersion", inversedBy="repetitions")
     * @ORM\JoinColumn(name="eventVersionID", referencedColumnName="eventVersionID")
     */
    protected $version;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEventRepetition", cascade={"persist"})
     * @ORM\JoinColumn(name="repetitionID", referencedColumnName="repetitionID")
     */
    protected $repetition;


    /**
     * @param $repetitionObject
     */
    public function __construct(CalendarEventVersion $version, CalendarEventRepetition $repetition)
    {
        $this->repetition = $repetition;
        $this->version = $version;
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


    public function getID()
    {
        return $this->versionRepetitionID;
    }


    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->repetition, $name], $arguments);
    }

    /**
     * @return mixed
     */
    public function getRepetition()
    {
        return $this->repetition;
    }


    public function __clone()
    {
        if ($this->versionRepetitionID) {
            $this->versionRepetitionID = null;
        }
    }

}