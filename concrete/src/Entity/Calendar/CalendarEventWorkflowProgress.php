<?php
namespace Concrete\Core\Entity\Calendar;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CalendarEventWorkflowProgress", indexes={
 * @ORM\Index(name="wpID", columns={"wpID"})
 * })
 */
class CalendarEventWorkflowProgress
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CalendarEvent", inversedBy="workflow_progress_objects")
     * @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     */
    protected $event;

    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned": true, "default": 0})
     */
    protected $wpID;


}
