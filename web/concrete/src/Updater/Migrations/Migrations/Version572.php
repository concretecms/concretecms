<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SinglePage;
use Exception;

class Version5711 extends AbstractMigration
{

    public function getName()
    {
        return '20141024000000';
    }

    public function up(Schema $schema)
    {

        /** Add query log db table */
        $ql = $schema->createTable('SystemDatabaseQueryLog');
        $ql->addColumn('query', 'text');
        $ql->addColumn('params', 'text', array('notnull' => false));
        $ql->addColumn('executionMS', 'string');

        /** Add query log single pages */
		$sp = Page::getByPath('/dashboard/system/optimization/query_log');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/system/optimization/query_log');
			$sp->update(array('cName' => 'Database Query Log'));
			$sp->setAttribute('meta_keywords', 'queries, database, mysql');
		}

        /** Refresh image block */
        $bt = BlockType::getByHandle('image');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    public function down(Schema $schema)
    {
    }
}
