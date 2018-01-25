<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Entity\Express\Form as ExpressForm;
use Concrete\Core\Entity\Package;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180119000000 extends AbstractMigration implements RepeatableMigrationInterface, DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            CalendarEventVersion::class,
            ExpressEntity::class,
            ExpressForm::class,
            Package::class,
        ]);
        $this->refreshDatabaseTables([
            'Workflows',
        ]);
    }
}
