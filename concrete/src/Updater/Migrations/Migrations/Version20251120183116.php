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
        // Empty migration. There used to be a twig block in here but
        // that block is too powerful to automatically be installed in the core
    }
}
