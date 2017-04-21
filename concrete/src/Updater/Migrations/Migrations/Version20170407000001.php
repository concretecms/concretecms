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
        $this->refreshEntities([
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\User\User'
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
