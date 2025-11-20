<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20251120183116 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $importer = new ContentImporter();
        $this->output(t('Adding TWIG support...'));
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/upgrade/twig.xml');
    }
}
