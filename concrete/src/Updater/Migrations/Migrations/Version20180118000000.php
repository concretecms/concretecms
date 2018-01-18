<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Page\PagePath;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20180118000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            PagePath::class,
            CalendarEventVersion::class,
        ]);
        $this->refreshDatabaseTables([
            'Groups',
            'Workflows',
        ]);
    }
}
