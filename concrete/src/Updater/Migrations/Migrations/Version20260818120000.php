<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Api\Command\SynchronizeScopesCommand;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

defined('C5_EXECUTE') or die('Access Denied.');

final class Version20260818120000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // Persist the OAuth scopes declared in the API specification (block_types:read is a new one)
        $this->app->executeCommand(new SynchronizeScopesCommand());
    }
}
