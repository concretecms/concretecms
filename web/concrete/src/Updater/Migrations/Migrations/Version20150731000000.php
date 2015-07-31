<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150731000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $table = $schema->getTable('SystemDatabaseQueryLog');
        $table->addColumn('ID', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->setPrimaryKey(array('ID'));
    }

    public function down(Schema $schema)
    {

    }

}
