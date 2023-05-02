<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Site\Service;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20220909000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * Refresh PageTypes table
     *
     * @return void
     */
    public function upgradeDatabase()
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'PageTypes',
        ]);
    }
}
