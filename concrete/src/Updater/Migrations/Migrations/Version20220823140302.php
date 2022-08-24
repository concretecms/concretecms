<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\InfoFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20220823140302 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $importer = new ContentImporter();
        $this->output(t('Importing upgrade/site_health.xml'));
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/upgrade/site_health.xml');

        $this->refreshEntities([
            Result::class,
            Finding::class,
            AlertFinding::class,
            InfoFinding::class,
            WarningFinding::class,
            SuccessFinding::class,
        ]);
    }
}
