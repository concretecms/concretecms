<?php
namespace Concrete\Core\Updater\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\DatabaseStructureManager;
use Doctrine\DBAL\Migrations\AbstractMigration as DoctrineAbstractMigration;
use Doctrine\ORM\Tools\SchemaTool;

abstract class AbstractMigration extends DoctrineAbstractMigration
{

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function refreshEntities($entities = null)
    {
        $em = $this->connection->getEntityManager();
        $sm = new DatabaseStructureManager($em);
        $sm->clearCacheAndProxies();

        $classes = array();
        $tool = new SchemaTool($em);
        foreach($entities as $entity) {
            $this->output(t('Refreshing schema for %s...', $entity));
            $classes[] = $em->getClassMetadata($entity);
        }

        $tool->updateSchema($classes, true);
    }

    protected function refreshDatabaseTables($tables)
    {
        $this->output(t('Updating database tables found in doctrine xml...'));
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema($tables);
    }

    protected function refreshBlockType($btHandle)
    {
        $this->output(t('Refreshing block type %s', $btHandle));
        $bt = BlockType::getByHandle($btHandle);
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

}
