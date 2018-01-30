<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Attribute\Key\EventKey;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent as EventEntity;

class EventOccurrenceList extends AttributedItemList
{
    protected $ev;

    protected $timezone;

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
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
        return EventOccurrence::getByID($row['versionOccurrenceID']);
    }

    public function filterByEvent(EventEntity $ev)
    {
        $this->query->andWhere('e.eventID = :eventID');
        $this->query->setParameter('eventID', $ev->getID());
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

    public function filterByKeywords($keywords)
    {
        $expressions = array(
            $this->query->expr()->like('ve.evName', ':keywords'),
            $this->query->expr()->like('ve.evDescription', ':keywords'),
        );

        $keys = EventKey::getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $this->query);
        }

        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    /**
     * @param bool $cancelled
     */
    public function filterByCancelled($cancelled)
    {
        $this->query->andWhere('eo.cancelled = :isCancelled');
        $this->query->setParameter('isCancelled', (bool) $cancelled);
    }

    public function groupByEvent()
    {
        $this->query->groupBy('e.eventID');
    }

    public function filterByCalendar($calendar)
    {
        if ($calendar instanceof Calendar) {
            $this->query->andWhere('e.caID = :caID');
            $this->query->setParameter('caID', $calendar->getID());
            $timezone = $calendar->getSite()->getConfigRepository()->get('timezone');
            if ($timezone) {
                $this->setTimezone($timezone);
            }
        } else if (is_array($calendar)) {
            $ids = array();
            foreach($calendar as $c) {
                $ids[] = $c->getID();
            }
            $this->query->andWhere(
                $this->query->expr()->in('e.caID', $ids)
            );
        }
    }

    public function filterByStartTimeAfter($startTime)
    {
        $this->query->andWhere('eo.startTime >= :startTimeAfter');
        $this->query->setParameter('startTimeAfter', $startTime);
    }

    public function filterByStartTimeBefore($startTime)
    {
        $this->query->andWhere('eo.startTime <= :startTimeBefore');
        $this->query->setParameter('startTimeBefore', $startTime);
    }

    public function filterByEndTimeAfter($endTime)
    {
        $this->query->andWhere('eo.endTime >= :endTimeAfter');
        $this->query->setParameter('endTimeAfter', $endTime);
    }

    public function filterByEndTimeBefore($endTime)
    {
        $this->query->andWhere('eo.endTime <= :endTimeBefore');
        $this->query->setParameter('endTimeBefore', $endTime);
    }

    public function filterByMonth($month, $year)
    {
        $eventsFrom = date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01'));
        $lastDayOfMonth = date("Y-m-t 23:59:59", strtotime($eventsFrom));
        $this->executePeriodFilter($eventsFrom, $lastDayOfMonth);
    }

    protected function executePeriodFilter($startOfPeriod, $endOfPeriod)
    {
        $service = \Core::make('date');
        if (isset($this->timezone)) {
            $timezone = $this->timezone;
        } else {
            $timezone = 'user';
        }
        $startOfPeriod = $service->toDateTime($startOfPeriod, 'UTC', $timezone)->getTimestamp();
        $endOfPeriod = $service->toDateTime($endOfPeriod, 'UTC', $timezone)->getTimestamp();

        $this->query->andWhere(
            $this->query->expr()->orX(
                $this->query->expr()->andX(
                    $this->query->expr()->comparison('eo.startTime', '<=', ':startOfPeriod'),
                    $this->query->expr()->comparison('eo.endTime', '>', ':startOfPeriod')
                ),
                $this->query->expr()->andX(
                    $this->query->expr()->comparison('eo.startTime', '<=', ':startOfPeriod'),
                    $this->query->expr()->comparison('eo.endTime', '>', ':endOfPeriod')
                ),
                $this->query->expr()->andX(
                    $this->query->expr()->comparison('eo.startTime', '>=', ':startOfPeriod'),
                    $this->query->expr()->comparison('eo.startTime', '<=', ':endOfPeriod')
                ),
                $this->query->expr()->andX(
                    $this->query->expr()->comparison('eo.startTime', '<=', ':startOfPeriod'),
                    $this->query->expr()->comparison('eo.endTime', '>=', ':endOfPeriod')
                )
            )
        );
        $this->query->setParameter('startOfPeriod', $startOfPeriod);
        $this->query->setParameter('endOfPeriod', $endOfPeriod);
    }

    public function filterByDateAfter(\DateTime $date)
    {
        $startOfPeriod = $date->getTimestamp();
        $this->query->andWhere(
            $this->query->expr()->orX(
                $this->query->expr()->andX(
                    $this->query->expr()->comparison('eo.startTime', '<=', ':startOfPeriod'),
                    $this->query->expr()->comparison('eo.endTime', '>', ':startOfPeriod')
                ),
                $this->query->expr()->comparison('eo.startTime', '>=', ':startOfPeriod')
            )
        );
        $this->query->setParameter('startOfPeriod', $startOfPeriod);
    }

    public function filterByDate($date)
    {

        $startOfPeriod = $date . ' 00:00:00';
        $endOfPeriod = $date . ' 23:59:59';
        $this->executePeriodFilter($startOfPeriod, $endOfPeriod);
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
        $this->query->select('vo.versionOccurrenceID')->from('CalendarEventVersionOccurrences', 'vo')
            ->innerJoin('vo', 'CalendarEventOccurrences', 'eo', 'vo.occurrenceID = eo.occurrenceID')
            ->innerJoin('vo', 'CalendarEventVersions', 've', 'vo.eventVersionID = ve.eventVersionID')
            ->innerJoin('ve', 'CalendarEvents', 'e', 've.eventID = e.eventID')
            ->leftJoin('e', 'CalendarEventSearchIndexAttributes', 'ea', 'e.eventID = ea.eventID');
        $this->query->orderBy('eo.startTime, ve.evName');
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
            $query->select('count(distinct eo.occurrenceID)')->setMaxResults(1);
        });

        return new Pagination($this, $adapter);
    }

    public function filterByTopic($topic)
    {
        if (is_object($topic)) {
            $treeNodeID = $topic->getTreeNodeID();
        } else {
            $treeNodeID = $topic;
        }
        $this->query->innerJoin('ve', 'CalendarEventVersionAttributeValues', 'cavTopics',
            've.eventVersionID = cavTopics.eventVersionID');
        $this->query->innerJoin('cavTopics', 'AttributeValues', 'av', 'cavTopics.avID = av.avID');
        $this->query->innerJoin('av', 'atSelectedTopics', 'atst', 'av.avID = atst.avID');
        $this->query->andWhere('atst.treeNodeID = :TopicNodeID');
        $this->query->setParameter('TopicNodeID', $treeNodeID);
    }
}
