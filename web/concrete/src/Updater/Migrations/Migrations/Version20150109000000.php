<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Block\BlockType\BlockType;

class Version20150109000000 extends AbstractMigration
{
    public function getDescription()
    {
        return '5.7.3.1';
    }

    public function up(Schema $schema)
    {
        $bt = BlockType::getByHandle('google_map');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }
}
