<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150713000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $db = \Database::connection();
        $db->Execute("update UserPointActions set upaHasCustomClass = 1 where upaHandle = 'won_badge'");
    }

    public function down(Schema $schema)
    {
    }
}
