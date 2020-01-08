<?php
namespace Concrete\Core\Board\Item\Populator;

use Concrete\Core\Board\Item\Data\CalendarEventData;
use Concrete\Core\Board\Item\Data\DataInterface;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventPopulator extends AbstractPopulator
{

    /**
     * @param Board $board
     * @param CalendarEventConfiguration $configuration
     * @return array
     */
    public function getDataObjects(Board $board, Configuration $configuration): array
    {
        $list = new EventOccurrenceList();
        $list->filterByCalendar($configuration->getCalendar());
        $list->setItemsPerPage(100);
        return $list->getResults();
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
