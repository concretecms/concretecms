<?php
namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\CalendarEventData;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventPopulator extends AbstractPopulator
{

    /**
     * @param Instance $instance
     * @param CalendarEventConfiguration $configuration
     * @param int $mode
     * @return array
     * @throws \Exception
     */
    public function getDataObjects(Instance $instance, Configuration $configuration, int $mode): array
    {
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
            throw new \Exception(t('No calendar found for board calendar event population!'));
        }
        $list = new EventOccurrenceList();
        $list->filterByCalendar($calendar);

        $treeNodeID = $configuration->getTopicTreeNodeID();
        if ($treeNodeID) {
            $list->filterByTopic($treeNodeID);
        }

        if ($mode == PopulatorInterface::RETRIEVE_FIRST_RUN) {
            // the first time we run we start today and go into the past.
            $list->getQueryObject()->orderBy('eo.startTime', 'desc');
        } else {
            $since = $instance->getDateDataPoolLastUpdated();
            $list->filterByStartTimeAfter($since);
        }

        $pagination = $list->getPagination();
        $pagination->setMaxPerPage(100)->setCurrentPage(1);

        return $pagination->getCurrentPageResults();
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
