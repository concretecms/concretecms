<?php

namespace Concrete\Core\Entity\Board\DataSource\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSourceConfigurationCalendarEvent")
 */
class CalendarEventConfiguration extends Configuration
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Calendar\Calendar")
     * @ORM\JoinColumn(name="caID", referencedColumnName="caID")
     */
    protected $calendar;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $topicTreeNodeID = 0;


    /**
     * @return mixed
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param mixed $calendar
     */
    public function setCalendar($calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * @return mixed
     */
    public function getTopicTreeNodeID()
    {
        return $this->topicTreeNodeID;
    }

    /**
     * @param mixed $topicTreeNodeID
     */
    public function setTopicTreeNodeID($topicTreeNodeID)
    {
        $this->topicTreeNodeID = $topicTreeNodeID;
    }







}
