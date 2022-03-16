<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\BoardPermissionAssignment;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Instance as BoardInstance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Board\ItemCategory;
use Concrete\Core\Entity\Board\ItemTag;
use Concrete\Core\Entity\Board\Template as BoardTemplate;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\Summary\CalendarEventTemplate;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Entity\Page\Container\Instance;
use Concrete\Core\Entity\Page\Container\InstanceArea;
use Concrete\Core\Entity\Page\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Page\Summary\PageTemplate;
use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\Field;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Entity\Summary\TemplateField;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20191029175713 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([ExpressKey::class]);
        $this->refreshEntities([
            Board::class,
            BoardPermissionAssignment::class,
            BoardInstance::class,
            InstanceItem::class,
            InstanceItemBatch::class,
            Item::class,
            ItemCategory::class,
            ItemTag::class,
            InstanceSlot::class,
            InstanceSlotRule::class,
            BoardTemplate::class,
            SlotTemplate::class,
            DataSource::class,
            ConfiguredDataSource::class,
            Configuration::class,
            PageConfiguration::class,
            CalendarEventConfiguration::class,
            Container::class,
            Instance::class,
            InstanceArea::class,
            Template::class,
            Category::class,
            Field::class,
            TemplateField::class,
            CustomPageTemplateCollection::class,
            PageTemplate::class,
            CalendarEvent::class,
            CalendarEventTemplate::class,
        ]);

        $this->output(t('Installing boards and containers upgrade XML...'));
        $importer = new ContentImporter();
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/upgrade/boards_containers.xml');

        $this->output(t('Installing core boards and summaries XML...'));
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/base/summary.xml');
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/base/boards.xml');

        // Install board permissions
    }
}
