<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20230308163514 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Don't worry, it'll just be remade as the editor with the new handle "default"
        $this->connection->executeStatement(
            "delete from Editor where handle = 'toast' and (pkgID = 0 or pkgID is null)"
        );
    }
}
