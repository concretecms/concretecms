<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Search\Pagination\Pagination;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class EventList extends \Concrete\Core\Search\ItemList\Database\AttributedItemList
{
    protected $autoSortColumns = array(
        'eventID',
        'name',
        'description',
    );

    /**
     * @return bool
     */
    public function isIncludeInactiveEvents()
    {
        return $this->includeInactiveEvents;
    }

    /**
     * @param bool $includeInactiveEvents
     */
    public function includeInactiveEvents()
    {
        $this->includeInactiveEvents = true;
    }

    protected $includeInactiveEvents = false;

    public function getResult($row)
    {
        return Event::getByID(array_get($row, 'eventID'));
    }

    public function filterByCalendar(Calendar $calendar)
    {
        $this->query->andWhere('e.caID = :caID');
        $this->query->setParameter('caID', $calendar->getID());
    }

    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        if ($this->includeInactiveEvents) {
            $query->andWhere('ve.eventVersionID = (select max(eventVersionID) from CalendarEventVersions where eventID = e.eventID)');
        } else {
            $query->andWhere('ve.evIsApproved = 1');
        }

        return $query;
    }

    /**
     * Returns the total results in this item list.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->select('count(distinct e.eventID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public function createQuery()
    {
        $this->query->select('e.eventID')->from('CalendarEvents', 'e')
            ->innerJoin('e', 'CalendarEventVersions', 've', 'e.eventID = ve.eventID')
            ->leftJoin('e', 'CalendarEventSearchIndexAttributes', 'ea', 'e.eventID = ea.eventID');
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\EventKey';
    }

    /**
     * @return \Concrete\Core\Search\Pagination\Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter(
            $this->deliverQueryObject(), function ($query) {
            $query->select('count(distinct e.eventID)')->setMaxResults(1);
        });

        return new Pagination($this, $adapter);
    }
}
