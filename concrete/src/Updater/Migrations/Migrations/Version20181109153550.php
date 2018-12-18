<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Validator\UsedString;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181109153550 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * Handle upgrading the database
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            UsedString::class
        ]);
    }

    /**
     * Handle downgrading the database
     */
    public function downgradeDatabase()
    {
        $this->refreshEntities();
    }
}
