<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20200625191946 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $bt = BlockType::getByHandle('gallery');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('gallery');
        }
    }

}
