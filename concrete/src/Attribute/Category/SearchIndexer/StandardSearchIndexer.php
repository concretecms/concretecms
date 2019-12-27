<?php

namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Schema\Schema;

class StandardSearchIndexer implements SearchIndexerInterface
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Database\Connection\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface::indexEntry()
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        if ($this->isValid($category)) {
            $attributeIndexer = $value->getAttributeKey()->getSearchIndexer();
            $attributeIndexer->indexEntry($category, $value, $subject);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface::clearIndexEntry()
     */
    public function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        if ($this->isValid($category)) {
            $attributeIndexer = $value->getAttributeKey()->getSearchIndexer();
            $attributeIndexer->clearIndexEntry($category, $value, $subject);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface::createRepository()
     */
    public function createRepository(CategoryInterface $category)
    {
        $schema = new Schema([], [], $this->connection->getSchemaManager()->createSchemaConfig());
        if ($this->isValid($category)) {
            /**
             * @var $category StandardSearchIndexerInterface
             */
            if (!$this->connection->tableExists($category->getIndexedSearchTable())) {
                $table = $schema->createTable($category->getIndexedSearchTable());
                $details = $category->getSearchIndexFieldDefinition();
                if (isset($details['columns'])) {
                    foreach ($details['columns'] as $column) {
                        $table->addColumn($column['name'], $column['type'], $column['options']);
                    }
                }
                if (isset($details['foreignKeys'])) {
                    foreach ($details['foreignKeys'] as $foreignKey) {
                        $options = [];
                        if (!empty($foreignKey['onUpdate'])) {
                            $options['onUpdate'] = $foreignKey['onUpdate'];
                        }
                        if (!empty($foreignKey['onDelete'])) {
                            $options['onDelete'] = $foreignKey['onDelete'];
                        }
                        $table->addForeignKeyConstraint($foreignKey['foreignTable'], $foreignKey['localColumns'], $foreignKey['foreignColumns'], $options);
                    }
                }

                if (isset($details['primary'])) {
                    $table->setPrimaryKey($details['primary']);
                }

                $queries = $schema->toSql($this->connection->getDatabasePlatform());
                foreach ($queries as $query) {
                    $this->connection->query($query);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface::updateRepositoryColumns()
     */
    public function updateRepositoryColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null)
    {
        if ($this->isValid($category)) {
            $attributeIndexer = $key->getSearchIndexer();
            $attributeIndexer->updateSearchIndexKeyColumns($category, $key, $previousHandle);
        }
    }

    /**
     * @deprecated use the updateRepositoryColumns() method
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key
     */
    public function refreshRepositoryColumns(CategoryInterface $category, AttributeKeyInterface $key)
    {
        if ($this->isValid($category)) {
            $attributeIndexer = $key->getSearchIndexer();
            $attributeIndexer->refreshSearchIndexKeyColumns($category, $key);
        }
    }

    /**
     * Check if a category is correctly configured to handle indexing stuff.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     *
     * @throws \Exception in case of errors
     *
     * @return bool
     */
    protected function isValid(CategoryInterface $category)
    {
        if (!($category instanceof StandardSearchIndexerInterface)) {
            throw new \Exception(t('Category %s must implement StandardSearchIndexerInterface.'), $category->getCategoryEntity()->getAttributeCategoryHandle());
        }

        return true;
    }
}
