<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20150619000000 extends AbstractMigration implements RepeatableMigrationInterface, DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->deleteInvalidForeignKey('AreaLayoutsUsingPresets', 'arLayoutID', 'AreaLayouts', 'arLayoutID');
        $this->refreshDatabaseTables([
            'AreaLayouts',
            'AreaLayoutsUsingPresets',
        ]);
    }
}
