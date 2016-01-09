<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Stack\StackList;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150619000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'AreaLayouts',
            'AreaLayoutsUsingPresets'
        ));
    }

    public function down(Schema $schema)
    {

    }


}
