<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Block\BlockType\BlockType;

class Version20200604000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // add gallery block type
        $bt = BlockType::getByHandle('gallery');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('gallery');
        }
    }
}
