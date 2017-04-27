<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170406000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $all_tables = $this->connection->executeQuery('SHOW TABLES LIKE "%ExpressSearchIndexAttributes"')->fetchAll();
        $all_tables = array_map(function($row) {
            return array_values($row)[0];
        }, $all_tables);

        $entity_tables = [];

        $entities = \Core::make('express')->getEntities(true)->findAll();
        foreach ($entities as $entity) {
            /* @var $entity \Concrete\Core\Entity\Express\Entity */
            $entity_tables[] = $entity->getAttributeKeyCategory()->getIndexedSearchTable();
        }

        $drop_tables = array_diff($all_tables, $entity_tables);

        foreach ($drop_tables as $table) {
            $this->connection->exec('DROP TABLE IF EXISTS ' . $table);
        }
    }

    public function down(Schema $schema)
    {
    }
}
