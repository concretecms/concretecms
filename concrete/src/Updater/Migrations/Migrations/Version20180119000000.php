<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Entity\Express\Form as ExpressForm;
use Concrete\Core\Entity\Package;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180119000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
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
