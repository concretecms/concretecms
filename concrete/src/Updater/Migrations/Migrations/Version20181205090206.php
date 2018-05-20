<?php

namespace Concrete\Core\Updater\Migrations\Migrations;


use Concrete\Core\Entity\Attribute\Key\UserKey;
use Concrete\Core\Entity\Attribute\Key\UserKeyPerUserGroup;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;


class Version20181205090206 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([UserKeyPerUserGroup::class,UserKey::class]);
    }

    public function downgradeDatabase()
    {
        $this->refreshEntities();
    }

}