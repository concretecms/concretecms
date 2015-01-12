<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class EventOccurrenceList extends ItemList
{

    protected $ev;

    public function getResult($row)
    {
        return EventOccurrence::getByID($row['occurrenceID']);
    }

    public function __construct(Event $ev)
    {
        $this->event = $ev;
        parent::__construct();
        $this->query->andWhere('eo.eventID = :eventID');
        $this->query->setParameter('eventID', $ev->getID());
    }

    /**
     * Returns the total results in this item list.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->select('count(distinct eo.occurrenceID)')->setMaxResults(1)->execute()->fetchColumn();

    }

    public function createQuery()
    {
        $this->query->select('eo.occurrenceID')->from('CalendarEventOccurrences', 'eo');
    }

    /**
     * @return \Concrete\Core\Search\Pagination\Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter(
            $this->deliverQueryObject(), function ($query) {
            $query->select('count(distinct eo.occurrenceID)')->setMaxResults(1);
        });
        return new Pagination($this, $adapter);
    }
}
