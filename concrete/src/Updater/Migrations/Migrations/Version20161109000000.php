<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161109000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        if (!$schema->getTable('Workflows')->hasColumn('pkgID')) {
            $schema->getTable('Workflows')->addColumn('pkgID', 'integer', array(
                'unsigned' => true, 'notnull' => true, 'default' => 0
            ));
        }
        if (!$schema->getTable('atSelectOptions')->hasColumn('isDeleted')) {
            $schema->getTable('atSelectOptions')->addColumn('isDeleted', 'boolean', array(
                'default' => 0
            ));
        }
    }

    public function down(Schema $schema)
    {
    }
}
