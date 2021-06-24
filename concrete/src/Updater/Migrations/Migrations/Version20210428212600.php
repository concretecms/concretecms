<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20210428212600 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->connection->executeUpdate('drop table if exists CollectionVersionFeatureAssignments');
        $this->connection->executeUpdate('drop table if exists BlockFeatureAssignments');
    }
}
