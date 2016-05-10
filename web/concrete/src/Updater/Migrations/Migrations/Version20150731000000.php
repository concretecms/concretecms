<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150731000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        try {
            $table = $schema->getTable('SystemDatabaseQueryLog');
            $table->addColumn('ID', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $table->setPrimaryKey(array('ID'));
        } catch (\Exception $e) {
        }

        $db = \Database::connection();
        $db->executeQuery("DELETE FROM FileSetFiles WHERE fID NOT IN (SELECT fID FROM Files)");
        $db->executeQuery("DELETE FROM FileSearchIndexAttributes WHERE fID NOT IN (SELECT fID FROM Files)");
        $db->executeQuery("DELETE FROM DownloadStatistics WHERE fID NOT IN (SELECT fID FROM Files)");
        $db->executeQuery("DELETE FROM FilePermissionAssignments WHERE fID NOT IN (SELECT fID FROM Files)");

        $bt = \BlockType::getByHandle('page_list');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }
}
