<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20210725000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // add breadcrumbs block type
        $bt = BlockType::getByHandle('breadcrumbs');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('breadcrumbs');

            // add breadcrumbs block to navigation set
            $set = BlockTypeSet::getByHandle('navigation');
            if (is_object($set)) {
                $set->addBlockType($bt);
            }
        }
    }
}
