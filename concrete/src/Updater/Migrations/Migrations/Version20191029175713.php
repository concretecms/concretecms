<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\Summary\CalendarEventTemplate;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Entity\Page\Summary\PageTemplate;
use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Page\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Entity\Page\Container\Instance;
use Concrete\Core\Entity\Page\Container\InstanceArea;

final class Version20191029175713 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/pages/containers');
        $this->createSinglePage('/dashboard/pages/containers/add', 'Add Container', 
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards');
        $this->createSinglePage('/dashboard/boards/boards', 'View Boards');
        $this->createSinglePage('/dashboard/boards/add', 'Add Board',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/details', 'Board Details',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/edit', 'Edit Board',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/appearance', 'Board Appearance',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/data_sources', 'Data Sources',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/weighting', 'Weighting',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/pool', 'Data Pool',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/permissions', 'Board Permissions',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/boards/instances', 'Board Instances',
            ['exclude_nav' => true, 'exclude_search_index' => true]
        );
        $this->createSinglePage('/dashboard/system/boards');
        $this->createSinglePage('/dashboard/system/boards/settings', 'Board Settings');
        $this->createSinglePage('/dashboard/system/boards/permissions', 'Board Permissions');
        $this->refreshEntities([
            Board::class,
            Item::class,
            DataSource::class,
            ConfiguredDataSource::class,
            PageConfiguration::class,
            CalendarEventConfiguration::class,
            Container::class,
            Instance::class,
            InstanceArea::class,
            Template::class,
            Category::class,
            CustomPageTemplateCollection::class,
            PageTemplate::class,
            CalendarEvent::class,
            CalendarEventTemplate::class,
        ]);
        $bt = BlockType::getByHandle('core_container');
        if (!$bt) {
            BlockType::installBlockType('core_container');
        } else {
            $bt->refresh();
        }
        $bt = BlockType::getByHandle('core_board_slot');
        if (!$bt) {
            BlockType::installBlockType('core_board_slot');
        } else {
            $bt->refresh();
        }
        
        $bt = BlockType::getByHandle('board');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('board');
            $multimediaSet = Set::getByHandle('multimedia');
            if ($multimediaSet) {
                $multimediaSet->addBlockType($bt);
            }
        } else {
            $bt->refresh();
        }


    }
}
