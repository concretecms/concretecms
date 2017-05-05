<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20170421000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\Search\SavedUserSearch',
            'Concrete\Core\Entity\Search\SavedPageSearch',
            'Concrete\Core\Entity\Search\SavedFileSearch',
        ]);
        $this->refreshBlockType('image');
    }

    public function down(Schema $schema)
    {
    }
}
