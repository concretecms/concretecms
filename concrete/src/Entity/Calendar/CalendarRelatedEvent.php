<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\Attribute\Key\Key;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarRelatedEvents")
 */
class CalendarRelatedEvent
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $relatedEventID;

    /**
     * @ORM\ManyToOne(targetEntity="CalendarEvent")
     * @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     */
    protected $event;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $relationID = 0;

    /**
     * Only type right now is S, series.
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $relationType;

    /**
     * @return mixed
     */
    public function getRelationID()
    {
        return $this->relationID;
    }



}