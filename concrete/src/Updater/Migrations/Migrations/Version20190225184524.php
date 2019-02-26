<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190225184524 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            AccessToken::class,
        ]);
    }
}
