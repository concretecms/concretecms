<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Designer\CustomElementItem;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElement;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElementItem;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20200626142348 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            CustomElement::class,
            ItemSelectorCustomElement::class,
            ItemSelectorCustomElementItem::class,
        ]);
        $this->createSinglePage('/dashboard/boards/designer', 'Designer');
        $this->createSinglePage('/dashboard/boards/designer/choose_items', 'Choose Items',
            ['exclude_nav' => true, 'exclude_search_index' => true]);
        $this->createSinglePage('/dashboard/boards/designer/customize_slot', 'Customize Slot',
            ['exclude_nav' => true, 'exclude_search_index' => true]);
        $this->createSinglePage('/dashboard/boards/scheduler', 'Scheduler');
        $this->createSinglePage('/dashboard/boards/instances/rules', 'Rules',
            ['exclude_nav' => true, 'exclude_search_index' => true]);
    }

}
