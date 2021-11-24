<?php

namespace Concrete\Core\Attribute\Key\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

class StandardSearchIndexer implements SearchIndexerInterface
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Doctrine\DBAL\Schema\Comparator
     */
    protected $comparator;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param \Doctrine\DBAL\Schema\Comparator $comparator
     */
    public function __construct(Connection $connection, Comparator $comparator)
    {
        $this->connection = $connection;
        $this->comparator = $comparator;
    }

    /**
     * @deprecated use the updateRepositoryColumns() method, with TRUE as the fourth argument
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key
     */
    public function refreshSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key)
    {
        $this->updateSearchIndexKeyColumns($category, $key);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface::updateSearchIndexKeyColumns()
     */
    public function updateSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null)
    {
        $indexTable = $category instanceof StandardSearchIndexerInterface || method_exists($category, 'getIndexedSearchTable') ? (string) $category->getIndexedSearchTable() : '';
        if ($indexTable === '') {
            // The attribute category doesn't support the indexing feature
            return false;
        }

        $controller = $key->getController();

        $definition = $controller->getSearchIndexFieldDefinition();
        if (!$definition) {
            // The attribute type doesn't support the indexing feature
            return false;
        }

        $dropColumns = [];
        if (((string) $previousHandle !== '' && $key->getAttributeKeyHandle() !== $previousHandle)) {
            // The handle of the attribute key changed: we need to drop the previous columns
            if (isset($definition['type'])) {
                $dropColumns[] = $this->getIndexEntryColumnName($previousHandle);
            } else {
                foreach (array_keys($definition) as $name) {
                    $dropColumns[] = $this->getIndexEntryColumnName($previousHandle, $name);
                }
            }
        } elseif (!$key->isAttributeKeySearchable()) {
            // The attribute key is (no more) searchable: we need to drop the previous columns
            if (isset($definition['type'])) {
                $dropColumns[] = $this->getIndexEntryColumnName($key->getAttributeKeyHandle());
            } else {
                foreach (array_keys($definition) as $name) {
                    $dropColumns[] = $this->getIndexEntryColumnName($key->getAttributeKeyHandle(), $name);
                }
            }
        }

        if (!$key->isAttributeKeySearchable() && $dropColumns === []) {
            // Nothing needs to be done/checked
            return false;
        }

        $sm = $this->connection->getSchemaManager();
        $fromTable = $sm->listTableDetails($indexTable);
        $toTable = clone $fromTable;

        array_walk(
            $dropColumns,
            function ($columnName) use ($toTable) {
                if ($toTable->hasIndex($columnName)) {
                    $toTable->dropIndex($columnName);
                }
                if ($toTable->hasColumn($columnName)) {
                    $toTable->dropColumn($columnName);
                }
            }
        );

        if ($key->isAttributeKeySearchable()) {
            if (isset($definition['type'])) {
                $this->processColumn(
                    $toTable,
                    $this->getIndexEntryColumnName($key->getAttributeKeyHandle()),
                    $definition['type'],
                    isset($definition['options']) ? $definition['options'] : []
                );
            } else {
                foreach ($definition as $name => $column) {
                    $this->processColumn(
                        $toTable,
                        $this->getIndexEntryColumnName($key->getAttributeKeyHandle(), $name),
                        $column['type'],
                        isset($column['options']) ? $column['options'] : []
                    );
                }
            }
        }

        // If attribute category has index definition
        if ($category instanceof StandardSearchIndexerInterface) {
            $categorySearchIndexFieldDefinition = $category->getSearchIndexFieldDefinition();
            if (
                is_array($categorySearchIndexFieldDefinition)
                && isset($categorySearchIndexFieldDefinition['index'])
                && is_array($categorySearchIndexFieldDefinition['index'])
                && in_array($key->getAttributeKeyHandle(), $categorySearchIndexFieldDefinition['index'])
            ) {
                $indexName = $this->getIndexEntryColumnName($key->getAttributeKeyHandle());
                if ($toTable->hasColumn($indexName) && !$toTable->hasIndex($indexName)) {
                    $toTable->addIndex([$indexName, 'cID'], $indexName);
                }
            }
        }

        $diff = $this->comparator->diffTable($fromTable, $toTable);
        if ($diff !== false) {
            $sql = $this->connection->getDatabasePlatform()->getAlterTableSQL($diff);
            foreach ($sql as $q) {
                $this->connection->exec($q);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface::indexEntry()
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns($category->getIndexedSearchTable());

        $attributeValue = $value->getSearchIndexValue();
        $details = $category->getSearchIndexFieldDefinition();
        $primary = $details['primary'][0];
        $primaryValue = $category->getIndexedSearchPrimaryKeyValue($subject);
        $columnValues = [];

        $exists = $this->connection->query(
            "select count({$primary}) from {$category->getIndexedSearchTable()} where {$primary} = {$primaryValue}"
        )->fetchColumn();

        if (is_array($attributeValue)) {
            foreach ($attributeValue as $valueKey => $valueValue) {
                $col = $this->getIndexEntryColumn($value->getAttributeKey(), $valueKey);
                if (isset($columns[strtolower($col)])) {
                    $columnValues[$col] = $valueValue;
                }
            }
        } else {
            $col = $this->getIndexEntryColumn($value->getAttributeKey());
            if (isset($columns[strtolower($col)])) {
                $columnValues[$col] = $attributeValue;
            }
        }

        if (count($columnValues)) {
            $primaries = [$primary => $primaryValue];

            if ($exists) {
                $this->connection->update(
                    $category->getIndexedSearchTable(),
                    $columnValues,
                    $primaries
                );
            } else {
                $this->connection->insert($category->getIndexedSearchTable(), $primaries + $columnValues);
            }
        }

        $controller = $value->getController();
        if ($controller instanceof TrackableInterface && $subject instanceof Collection) {
            $tracker = app('statistics/tracker');
            $tracker->track($subject);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface::clearIndexEntry()
     */
    public function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        $key = $value->getAttributeKey();
        if (!$key->isAttributeKeySearchable()) {
            return false; // if it's not searchable there won't be the right columns in the database
        }

        $definition = $key->getController()->getSearchIndexFieldDefinition();
        if (!$definition) {
            return false;
        }
        $details = $category->getSearchIndexFieldDefinition();
        $primary = $details['primary'][0];
        $primaryValue = $category->getIndexedSearchPrimaryKeyValue($subject);
        $columnValues = [];

        if (isset($definition['type'])) {
            $col = $this->getIndexEntryColumn($key);
            $columnValues[$col] = null;
        } else {
            $subkeys = array_keys($definition);
            foreach ($subkeys as $subkey) {
                $col = $this->getIndexEntryColumn($key, $subkey);
                $columnValues[$col] = null;
            }
        }

        if (count($columnValues)) {
            $primaries = [$primary => $primaryValue];

            $this->connection->update(
                $category->getIndexedSearchTable(),
                $columnValues,
                $primaries
            );
        }
    }

    /**
     * Get the name of the column associated to an attribute key.
     *
     * @param string $attributeKeyHandle the handle of the attribute key
     * @param string $subKey the part of the name of a sub-field (if any - to be used for example if an attribute key needs multiple columns)
     *
     * @return string
     */
    protected function getIndexEntryColumnName($attributeKeyHandle, $subKey = '')
    {
        return (string) $subKey === '' ? "ak_{$attributeKeyHandle}" : "ak_{$attributeKeyHandle}_{$subKey}";
    }

    /**
     * @deprecated use the getIndexEntryColumnName() method
     *
     * @param \Concrete\Core\Entity\Attribute\Key\Key $key
     * @param string|false $subKey
     *
     * @return string
     */
    protected function getIndexEntryColumn(Key $key, $subKey = false)
    {
        return $this->getIndexEntryColumnName($key->getAttributeKeyHandle(), (string) $subKey);
    }

    /**
     * Set the 'length' key of an array containing the column options.
     *
     * For certain fields (eg TEXT) Doctrine uses the length of the longest column to determine what field type to use.
     * For search indexing even if we may not currently have something long in a column,
     * we need the longest possible column so that we don't truncate any data.
     *
     * @param array $options
     *
     * @return array the $options argument, with the 'length' key set (if needed)
     */
    private function setTypeLength(array $options)
    {
        // If we have explicitly set a length, use it
        if (isset($options['length']) && $options['length']) {
            return $options;
        }
        if ($options['type']->getName() == 'text') {
            $options['length'] = MySqlPlatform::LENGTH_LIMIT_MEDIUMTEXT + 1; // This forces Doctrine to use `LONGTEXT` instead of `TINYTEXT`
        }

        return $options;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Table $toTable
     * @param string $columnName
     * @param string $typeName
     * @param array $options
     */
    private function processColumn(Table $toTable, $columnName, $typeName, array $options)
    {
        if ($toTable->hasColumn($columnName)) {
            $toTable->changeColumn(
                $columnName,
                $this->setTypeLength($options + ['type' => Type::getType($typeName)])
            );
        } else {
            $toTable->addColumn(
                $columnName,
                $typeName,
                $options
            );
        }
    }
}
