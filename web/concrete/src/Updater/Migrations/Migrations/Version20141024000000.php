<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use SinglePage;
use Exception;

class Version20141024000000 extends AbstractMigration
{
    public function getDescription()
    {
        return '5.7.2';
    }

    public function up(Schema $schema)
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
            $ql->addColumn('params', 'text', array('notnull' => false));
            $ql->addColumn('executionMS', 'string');
        }

        /* Add query log single pages */
        $sp = Page::getByPath('/dashboard/system/optimization/query_log');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/optimization/query_log');
            $sp->update(array('cName' => 'Database Query Log'));
            $sp->setAttribute('meta_keywords', 'queries, database, mysql');
        }

        /* Refresh image block */
        $bt = BlockType::getByHandle('image');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }
}
