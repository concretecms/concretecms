<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170123000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->refreshDatabaseTables(['FileImageThumbnailPaths']);
    }

    public function down(Schema $schema)
    {
    }
}
