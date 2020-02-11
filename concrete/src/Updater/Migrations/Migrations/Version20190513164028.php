<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;


class Version20190513164028 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema)
    {
        $this->refreshBlockType('survey');
    }
}
