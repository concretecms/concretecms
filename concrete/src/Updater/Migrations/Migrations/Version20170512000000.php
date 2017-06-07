<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170512000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshBlockType('autonav');
        $this->refreshBlockType('core_conversation');
        $this->refreshBlockType('google_map');
        $this->refreshBlockType('page_list');
    }

    public function down(Schema $schema)
    {
    }
}
