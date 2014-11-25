<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use \Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Block\BlockType\BlockType;

class Version5722 extends AbstractMigration
{

    public function getName()
    {
        return '20141121000000';
    }

    public function up(Schema $schema)
    {
        $ft = FlagType::getByhandle('spam');
        if (!is_object($ft)) {
            FlagType::add('spam');
        }

        $bt = BlockType::getByHandle('image_slider');
        $bt->refresh();
    }

    public function down(Schema $schema)
    {
    }
}
