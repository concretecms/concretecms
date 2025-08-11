<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Site\Service;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20220909300000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * Refresh event list block type with sort order functionality
     *
     * @return void
     */
    public function upgradeDatabase()
    {
        $this->refreshBlockType('event_list');
    }
}
