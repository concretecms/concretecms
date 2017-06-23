<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20170407000001 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery('set foreign_key_checks = 0');
        $this->refreshEntities([
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\User\User'
        ]);
        $this->connection->executeQuery('set foreign_key_checks = 1');
    }

    public function down(Schema $schema)
    {
    }
}
