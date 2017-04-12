<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20170412000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $em = ORM::entityManager();
        $manager = new DatabaseStructureManager($em);
        $manager->refreshEntities();
    }

    public function down(Schema $schema)
    {
    }
}
