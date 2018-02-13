<?php

namespace Concrete\Core\Updater\Migrations;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\DBAL\Migrations\AbstractMigration as DoctrineAbstractMigration;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;

abstract class AbstractMigration extends DoctrineAbstractMigration
{
    protected $app;

    protected $validAttributes = [];

    public function __construct(Version $version)
    {
        parent::__construct($version);
        $app = Facade::getFacadeApplication();
        $this->app = $app;
    }

    /**
     * Override this method when the database structure is upgraded using ONLY the DBAL Schema object.
     *
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema)
    {
    }

    /**
     * Override this method when database schema is not upgraded, or when it's upgraded without using a Schema.
     *
     * @param Schema $schema
     */
    public function upgradeDatabase()
    {
    }

    /**
     * Override this method when the database structure is downgraded using ONLY the DBAL Schema object.
     *
     * @param Schema $schema
     */
    public function downgradeSchema(Schema $schema)
    {
    }

    /**
     * Override this method when database schema is not downgraded, or when it's downgraded without using a Schema.
     */
    public function downgradeDatabase()
    {
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::up()
     */
    final public function up(Schema $schema)
    {
        $this->upgradeSchema($schema);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postUp()
     */
    final public function postUp(Schema $schema)
    {
        $this->upgradeDatabase();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    final public function down(Schema $schema)
    {
        $this->downgradeSchema($schema);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::postDown()
     */
    final public function postDown(Schema $schema)
    {
        $this->downgradeDatabase();
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

    protected function isAttributeHandleValid($categoryClass, $handle)
    {
        if (!isset($this->validAttributes[$categoryClass])) {
            $this->validAttributes[$categoryClass] = [];
        }
        if (!isset($this->validAttributes[$categoryClass][$handle])) {
            $app = Application::getFacadeApplication();
            $category = $app->make($categoryClass);
            $this->validAttributes[$categoryClass][$handle] = $category->getAttributeKeyByHandle($handle) ? true : false;
        }

        return $this->validAttributes[$categoryClass][$handle];
    }

    /**
     * Create a new SinglePage (if it does not exist).
     *
     * @param string $path the single page path
     * @param string $name the single page name
     * @param array $attributes the attribute values (keys are the attribute handles, values are the attribute values)

     *
     * @return \Concrete\Core\Page\Page
     */
    protected function createSinglePage($path, $name = '', array $attributes = [])
    {
        $sp = Page::getByPath($path);
        if (!is_object($sp) || $sp->isError()) {
            $this->output(t('Creating single page at %s...', $path));
            $sp = SinglePage::add($path);
            $update = [];
            $name = (string) $name;
            if ($name !== '') {
                $update['cName'] = $name;
            }
            if (array_key_exists('cDescription', $attributes)) {
                $description = (string) $attributes['cDescription'];
                unset($attributes['cDescription']);
                if ($description !== '') {
                    $update['cDescription'] = $description;
                }
            }
            if (count($update) > 0) {
                $sp->update($update);
            }
            foreach ($attributes as $attributeHandle => $attributeValue) {
                if ($this->isAttributeHandleValid(PageCategory::class, $attributeHandle)) {
                    $sp->setAttribute($attributeHandle, $attributeValue);
                }
            }
        }

        return $sp;
    }
}
