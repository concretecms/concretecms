<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarEventOccurrences",
 *  indexes={
 *     @ORM\Index(name="eventdates", columns={"occurrenceID", "startTime", "endTime"})
 *     }
 * )
 */
class CalendarEventOccurrence
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $occurrenceID;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEventRepetition")
     * @ORM\JoinColumn(name="repetitionID", referencedColumnName="repetitionID")
     */
    protected $repetition;

    /**
     * @ORM\Column(type="integer")
     */
    protected $startTime = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $endTime = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $cancelled = false;


    public function __construct(CalendarEventRepetition $repetition, $start, $end, $cancelled = false)
    {
        $this->repetition = $repetition;
        $this->startTime = $start;
        $this->endTime = $end;
        $this->cancelled = (bool) $cancelled;
    }

    public function cancel()
    {
        $this->setCancelled(true);
    }

    /**
     * @return CalendarEventVersionRepetition
     */
    public function getRepetitionEntity()
    {
        return $this->repetition;
    }

    public function getRepetition()
    {
        return $this->repetition->getRepetitionObject();
    }

    /**
     * @param CalendarEventVersionRepetition $repetition
     */
    public function setRepetition($repetition)
    {
        $this->repetition = $repetition;
    }


    /**
     * @return int
     */
    public function getStart()
    {
        return $this->startTime;
    }

    /**
     * @param int $start
     */
    public function setStart($start)
    {
        $this->startTime = $start;
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->endTime;
    }

    /**
     * @param int $end
     */
    public function setEnd($end)
    {
        $this->endTime = $end;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return (bool) $this->cancelled;
    }

    public function isAllDay()
    {
        $diff = $this->getEnd() - $this->getStart();
        if ($diff == 0) {
            return true;
        }

        return false;
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
     * @param bool $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
    }

    public function getID()
    {
        return $this->occurrenceID;
    }

    public function __clone()
    {
        if ($this->occurrenceID) {
            $this->occurrenceID = null;
        }
    }


}