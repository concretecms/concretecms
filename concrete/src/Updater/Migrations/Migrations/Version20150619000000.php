<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * @since 5.7.5
 */
class Version20150619000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     * @since 8.3.2
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
