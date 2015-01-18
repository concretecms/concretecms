<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version5732 extends AbstractMigration
{

    public function getName()
    {
        return '20150117000000';
    }

    public function up(Schema $schema)
    {
        $db = \Database::get();
        $db->Execute('DROP TABLE IF EXISTS PageStatistics');
    }

    public function down(Schema $schema)
    {
    }
}
