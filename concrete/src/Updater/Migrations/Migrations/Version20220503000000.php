<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Site\Service;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20220503000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * Refresh video block type for new title field
     *
     * @return void
     */
    public function upgradeDatabase()
    {
        $this->refreshBlockType('video');
    }
}
