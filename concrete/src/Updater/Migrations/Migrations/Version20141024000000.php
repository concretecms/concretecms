<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Exception;

class Version20141024000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.2';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        /* Add query log db table */

        try {
            $table = $schema->getTable('SystemDatabaseQueryLog');
        } catch (Exception $e) {
            $table = null;
        }
        if (!($table instanceof Table)) {
            $ql = $schema->createTable('SystemDatabaseQueryLog');
            $ql->addColumn('query', 'text');
            $ql->addColumn('params', 'text', ['notnull' => false]);
            $ql->addColumn('executionMS', 'string');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        /* Add query log single pages */
        $this->createSinglePage('/dashboard/system/optimization/query_log', 'Database Query Log', ['meta_keywords' => 'queries, database, mysql']);

        /* Refresh image block */
        $this->refreshBlockType('image');
    }
}
