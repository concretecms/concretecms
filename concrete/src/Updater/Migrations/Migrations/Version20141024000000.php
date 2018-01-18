<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;
use Concrete\Core\Updater\Migrations\ManagedSchemaUpgraderInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Exception;
use SinglePage;

class Version20141024000000 extends AbstractMigration implements ManagedSchemaUpgraderInterface, DirectSchemaUpgraderInterface
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
     * @see \Concrete\Core\Updater\Migrations\ManagedSchemaUpgraderInterface::upgradeSchema()
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
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        /* Add query log single pages */
        $sp = Page::getByPath('/dashboard/system/optimization/query_log');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/optimization/query_log');
            $sp->update(['cName' => 'Database Query Log']);
            $sp->setAttribute('meta_keywords', 'queries, database, mysql');
        }

        /* Refresh image block */
        $bt = BlockType::getByHandle('image');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }
}
