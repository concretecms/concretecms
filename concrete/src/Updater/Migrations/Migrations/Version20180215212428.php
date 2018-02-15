<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180215212428 extends AbstractMigration
{
    public function upgradeDatabase()
    {
        $this->connection->executeQuery('drop table if exists QueueMessages');
        $this->connection->executeQuery('drop table if exists Queues');
        $this->refreshDatabaseTables([
            'Queues',
            'QueueMessages'
        ]);
    }

}
