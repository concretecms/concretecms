<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170505000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshDatabaseTables(['Stacks']);
    }

    public function down(Schema $schema)
    {
    }
}
