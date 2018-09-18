<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;

class Version20180615000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {

        // add desktop_draft_list block type
        $bt = BlockType::getByHandle('desktop_draft_list');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_draft_list');
        }

        // add core_desktop block type set
        $desktopSet = BlockTypeSet::getByHandle('core_desktop');
        if (!is_object($desktopSet)) {
            $desktopSet = BlockTypeSet::add('core_desktop', 'Desktop');
        }

        // add desktop_draft_list block type to core_desktop set
        $desktopSet->addBlockType($bt);
    }
}
