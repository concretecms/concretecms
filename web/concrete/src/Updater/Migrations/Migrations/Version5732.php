<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version5732 extends AbstractMigration
{

    public function getName()
    {
        return '20150121000000';
    }

    public function up(Schema $schema)
    {
        $db = \Database::get();
        $db->Execute('DROP TABLE IF EXISTS PageStatistics');

        // TODO: Convert database PermissionDuration objects to new class signature.
    }

    public function down(Schema $schema)
    {
    }
}
