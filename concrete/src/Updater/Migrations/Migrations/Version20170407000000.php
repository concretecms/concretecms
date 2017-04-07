<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170407000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            Entity::class
        ]);
    }

    public function down(Schema $schema)
    {
    }
}
