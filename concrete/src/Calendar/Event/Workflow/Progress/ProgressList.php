<?php
namespace Concrete\Core\Calendar\Event\Workflow\Progress;

use Concrete\Core\Calendar\Event\EventList;
use Concrete\Core\Workflow\Progress\CalendarEventProgress;

class ProgressList extends EventList
{
    public function createQuery()
    {
        parent::createQuery();
        $this->query->innerJoin('e', 'CalendarEventWorkflowProgress', 'cwp', 'e.eventID = cwp.eventID');
        $this->query->innerJoin('cwp', 'WorkflowProgress', 'wp', 'cwp.wpID = wp.wpID');
        $this->query->andWhere('wp.wpIsCompleted = :wpIsCompleted');
        $this->query->setParameter('wpIsCompleted', false);
    }

    public function getResult($row)
    {
        $event = parent::getResult($row);
        $wp = CalendarEventProgress::getByID($row['wpID']);
        $progressEvent = new Event($event, $wp);

        return $progressEvent;
    }
}
