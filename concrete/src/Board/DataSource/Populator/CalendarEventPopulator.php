<?php
namespace Concrete\Core\Board\DataSource\Populator;

use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Calendar\CalendarEvent;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventPopulator implements PopulatorInterface
{

    /**
     * @param Board $board
     * @param CalendarEventConfiguration $configuration
     * @return array
     */
    public function getDataSourceObjects(Board $board, Configuration $configuration): array
    {
        return [];
    }

    /**
     * @param CalendarEvent $mixed
     * @return Block
     */
    public function createBoardItemBlock($mixed): Block
    {
        return null;
    }

    /**
     * @param CalendarEvent $mixed
     * @return int
     */
    public function getObjectRelevantDate($mixed) : int
    {
        return 0;
    }

}
