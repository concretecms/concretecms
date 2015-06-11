<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20150610000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $bt = \BlockType::getByHandle('file');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }


}
