<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\User\User;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20201028143317 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            User::class
        ]);

        $this->connection->executeQuery("ALTER Table UserPermissionEditPropertyAccessList ADD COLUMN uHomeFileManagerFolderID tinyint(1) DEFAULT '0'");
    }
}