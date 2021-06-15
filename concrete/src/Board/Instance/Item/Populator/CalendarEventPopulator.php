<?php
namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\CalendarEventData;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Search\Field\Field\SiteField;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventPopulator extends AbstractPopulator
{

    /**
     * @param Instance $instance
     * @param CalendarEventConfiguration $configuration
     * @return array
     * @throws \Exception
     */
    public function getDataObjects(Instance $instance, ConfiguredDataSource $dataSource): array
    {
        $configuration = $dataSource->getConfiguration();
        // @TODO We need to fix this: if our configuration has no calendar, we need to get the calendar
        // from the site. But in this case, we're just going to temporarily get the first calendar
        // from the site that we can find
        $calendar = $configuration->getCalendar();
        if (!$calendar) {
            $site = $instance->getSite();
            $calendars = app(EntityManager::class)->getRepository(Calendar::class)
                ->findbySite($site);
            if (isset($calendars[0])) {
                $calendar = $calendars[0];
            }
        }
        if (!$calendar) {
            return [];
        }

        $list = new EventOccurrenceList();
        $list->filterByCalendar($calendar);

        $query = $configuration->getQuery();
        if ($query) {
            $fields = $query->getFields();
            if ($fields) {
                foreach ($fields as $field) {
                    $field->filterList($list);
                }
            }
        }

        $future = $this->getPopulationDayIntervalFutureDatetime($dataSource, $instance);
        $past = $this->getPopulationDayIntervalPastDatetime($dataSource, $instance);
        $list->getQueryObject()->andWhere('eo.startTime <= :future');
        $list->getQueryObject()->andWhere('eo.startTime >= :past');
        $list->getQueryObject()->setParameter('future', $future->getTimestamp());
        $list->getQueryObject()->setParameter('past', $past->getTimestamp());

        $pagination = $list->getPagination();
        $pagination->setMaxPerPage(1000)->setCurrentPage(1);
        return $pagination->getCurrentPageResults();

        /* this is old logic, remove once we're sure this works
        if ($mode == PopulatorInterface::RETRIEVE_FIRST_RUN) {

            // the first time we run we start today and go into the past.
            $list->getQueryObject()->orderBy('eo.startTime', 'desc');
            $list->getQueryObject()->andWhere('eo.startTime < :now');
            $list->getQueryObject()->setParameter('now', time());

        } else {
            $since = $instance->getDateDataPoolLastUpdated();
            $list->filterByStartTimeAfter($since);
        }

        $pagination = $list->getPagination();
        $pagination->setMaxPerPage(100)->setCurrentPage(1);
        return $pagination->getCurrentPageResults();
        */
    }

    public function getObjectUniqueItemId($mixed): ?string
    {
        return $mixed->getID();
    }

    public function getObjectRelevantThumbnail($mixed): ?File
    {
        $config = app('config');
        $handle = $config->get('concrete.calendar.summary_thumbnail_attribute');
        if (!$handle) {
            $handle = 'event_thumbnail';
        }
        $thumbnail = $mixed->getAttribute($handle);
        return $thumbnail;
    }

    /**
     * @param CalendarEventVersionOccurrence $mixed
     * @return int
     */
    public function getObjectRelevantDate($mixed) : int
    {
        return $mixed->getOccurrence()->getStart();
    }

    /**
     * @param CalendarEventVersionOccurrence $mixed
     * @return null|string
     */
    public function getObjectName($mixed): ?string
    {
        return $mixed->getEvent()->getName();
    }

    /**
     * @param CalendarEventVersionOccurrence $mixed
     * @return DataInterface
     */
    public function getObjectData($mixed): DataInterface
    {
        return new CalendarEventData($mixed);
    }

    public function getObjectCategories($mixed): array
    {
        return [];
    }

    public function getObjectTags($mixed): array
    {
        return [];
    }


}
