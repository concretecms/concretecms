<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Notification\UserDeactivatedNotification;
use Concrete\Core\Entity\User\LoginAttempt;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181029223809 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            LoginAttempt::class,
            UserDeactivatedNotification::class
        ]);
    }

    public function downgradeDatabase()
    {
        $this->refreshEntities();
    }
}
