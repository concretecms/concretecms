<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20150619000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        if ($this->connection->getSchemaManager()->tablesExist(['AreaLayoutsUsingPresets'])) {
            $this->deleteInvalidForeignKey('AreaLayoutsUsingPresets', 'arLayoutID', 'AreaLayouts', 'arLayoutID');
        }
        $this->refreshDatabaseTables([
            'AreaLayouts',
            'AreaLayoutsUsingPresets',
        ]);
    }
}
