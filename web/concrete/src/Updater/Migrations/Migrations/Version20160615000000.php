<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20160615000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $bt = BlockType::getByHandle('page_list');
        if (is_object($bt)) {
            $bt->refresh();
        }
        $bt = BlockType::getByHandle('form');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }


}
