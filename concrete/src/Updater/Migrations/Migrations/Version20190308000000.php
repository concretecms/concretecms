<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20190308000000 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->connection->executeQuery('drop table if exists QueueMessages');
        $this->connection->executeQuery('drop table if exists Queues');
        $this->refreshDatabaseTables([
            'Queues',
            'QueueMessages'
        ]);
        $this->refreshEntities([Batch::class]);
    }
}
