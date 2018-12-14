<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Entity\User\UserSignup;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181214065452 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
{
    $this->refreshEntities([
        UserSignup::class,
    ]);
}

    public function downgradeDatabase()
{
    $this->refreshEntities();
}
}
