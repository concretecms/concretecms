<?php

namespace Concrete\Core\Updater\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\DBAL\Migrations\AbstractMigration as DoctrineAbstractMigration;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;

abstract class AbstractMigration extends DoctrineAbstractMigration
{
    protected $app;

    public function __construct(Version $version)
    {
        parent::__construct($version);
        $app = Facade::getFacadeApplication();
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::up()
     */
    final public function up(Schema $schema)
    {
        if ($this instanceof ManagedSchemaUpgraderInterface) {
            $this->upgradeSchema($schema);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postUp()
     */
    final public function postUp(Schema $schema)
    {
        if ($this instanceof DirectSchemaUpgraderInterface) {
            $this->upgradeDatabase();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    final public function down(Schema $schema)
    {
        if ($this instanceof ManagedSchemaDowngraderInterface) {
            $this->downgradeSchema($schema);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postDown()
     */
    final public function postDown(Schema $schema)
    {
        if ($this instanceof DirectSchemaDowngraderInterface) {
            $this->downgradeDatabase();
        }
    }

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function refreshEntities($entities = null)
    {
        $em = $this->connection->getEntityManager();
        $sm = new DatabaseStructureManager($em);
        $sm->clearCacheAndProxies();

        $classes = [];
        $tool = new SchemaTool($em);
        foreach ($entities as $entity) {
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

    /**
     * Set to NULL the fields in a table that reference not existing values of another table.
     *
     * @param string $table The table containing the problematic field
     * @param string $field The problematic field
     * @param string $linkedTable The referenced table
     * @param string $linkedField The referenced field
     */
    protected function nullifyInvalidForeignKey($table, $field, $linkedTable, $linkedField)
    {
        $platform = $this->connection->getDatabasePlatform();
        $sqlTable = $platform->quoteSingleIdentifier($table);
        $sqlField = $platform->quoteSingleIdentifier($field);
        $sqlLinkedTable = $platform->quoteSingleIdentifier($linkedTable);
        $sqlLinkedField = $platform->quoteSingleIdentifier($linkedField);
        $this->connection->executeQuery("
            update {$sqlTable}
            left join {$sqlLinkedTable} on {$sqlTable}.{$sqlField} = {$sqlLinkedTable}.{$sqlLinkedField}
            set {$sqlTable}.{$sqlField} = null
            where {$sqlLinkedTable}.{$sqlLinkedField} is null
        ");
    }

    /**
     * Delete the records in a table whose field references not existing values of another table.
     *
     * @param string $table The table containing the problematic field
     * @param string $field The problematic field
     * @param string $linkedTable The referenced table
     * @param string $linkedField The referenced field
     */
    protected function deleteInvalidForeignKey($table, $field, $linkedTable, $linkedField)
    {
        $platform = $this->connection->getDatabasePlatform();
        $sqlTable = $platform->quoteSingleIdentifier($table);
        $sqlField = $platform->quoteSingleIdentifier($field);
        $sqlLinkedTable = $platform->quoteSingleIdentifier($linkedTable);
        $sqlLinkedField = $platform->quoteSingleIdentifier($linkedField);
        $this->connection->executeQuery("
            delete {$sqlTable}
            from {$sqlTable}
            left join {$sqlLinkedTable} on {$sqlTable}.{$sqlField} = {$sqlLinkedTable}.{$sqlLinkedField}
            where {$sqlLinkedTable}.{$sqlLinkedField} is null
        ");
    }
}
