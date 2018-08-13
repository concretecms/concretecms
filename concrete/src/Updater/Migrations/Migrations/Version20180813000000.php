<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180813000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeDatabase()
    {
        $this->refreshBlockType('express_entry_list');
    }
}