<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170505000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $stacks = $schema->getTable('Stacks');
        if ($stacks->hasColumn('siteTreeID')) {
            $stacks->dropColumn('siteTreeID');
        }
    }

    public function down(Schema $schema)
    {
    }
}
