<?php
namespace Concrete\Core\Board\Item\Populator;

use Concrete\Core\Calendar\Event\EventList;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Calendar\CalendarEvent;

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
        $list = new EventList();
        $list->filterByCalendar($configuration->getCalendar());
        $list->setItemsPerPage(100);
        return $list->getResults();
    }

    /**
     * @param CalendarEvent $mixed
     * @return int
     */
    public function getObjectRelevantDate($mixed) : int
    {
        return $mixed->getOccurrences()[0]->getStart();
    }

    /**
     * @param CalendarEvent $mixed
     * @return null|string
     */
    public function getObjectName($mixed): ?string
    {
        $version = $mixed->getApprovedVersion();
        if ($version) {
            return $version->getName();
        }
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
