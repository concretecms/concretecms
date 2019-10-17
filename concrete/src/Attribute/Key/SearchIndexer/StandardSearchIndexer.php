<?php

namespace Concrete\Core\Attribute\Key\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Comparator;
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
     * Refresh the Search Index columns (if there are schema changes for example).
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key
     */
    public function refreshSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key)
    {
        $controller = $key->getController();

        if ($key->isAttributeKeySearchable() == false ||
            $category->getIndexedSearchTable() == false ||
            $controller->getSearchIndexFieldDefinition() == false) {
            return false;
        }

        $definition = $controller->getSearchIndexFieldDefinition();
        $sm = $this->connection->getSchemaManager();
        $fromTable = $sm->listTableDetails($category->getIndexedSearchTable());
        $toTable = $sm->listTableDetails($category->getIndexedSearchTable());

        if (isset($definition['type'])) {
            $options = [
                'type' => Type::getType($definition['type']),
            ];
            $options = array_merge($options, $definition['options']);
            $options = $this->setTypeLength($options);
            $toTable->changeColumn('ak_' . $key->getAttributeKeyHandle(), $options);
        } else {
            foreach ($definition as $name => $column) {
                $options = [
                    'type' => Type::getType($column['type']),
                ];
                $options = array_merge($options, $column['options']);
                $options = $this->setTypeLength($options);
                $toTable->changeColumn('ak_' . $key->getAttributeKeyHandle() . '_' . $name, $options);
            }
        }
        $comparator = $this->comparator;
        $diff = $comparator->diffTable($fromTable, $toTable);
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
     * @see \Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface::updateSearchIndexKeyColumns()
     */
    public function updateSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null)
    {
        $controller = $key->getController();

        if ($key->getAttributeKeyHandle() == $previousHandle ||
            $key->isAttributeKeySearchable() == false ||
            $category->getIndexedSearchTable() == false ||
            $controller->getSearchIndexFieldDefinition() == false) {
            return false;
        }

        $fields = [];
        $dropColumns = [];
        $definition = $controller->getSearchIndexFieldDefinition();

        $sm = $this->connection->getSchemaManager();
        $toTable = $sm->listTableDetails($category->getIndexedSearchTable());

        if ($previousHandle) {
            if (isset($definition['type'])) {
                $dropColumns[] = 'ak_' . $previousHandle;
            } else {
                foreach ($definition as $name => $column) {
                    $dropColumns[] = 'ak_' . $previousHandle . '_' . $name;
                }
            }
        }

        if (isset($definition['type'])) {
            if (!$toTable->hasColumn('ak_' . $key->getAttributeKeyHandle())) {
                $fields[] = [
                    'name' => 'ak_' . $key->getAttributeKeyHandle(),
                    'type' => $definition['type'],
                    'options' => $definition['options'],
                ];
            }
        } else {
            foreach ($definition as $name => $column) {
                if (!$toTable->hasColumn('ak_' . $key->getAttributeKeyHandle() . '_' . $name)) {
                    $fields[] = [
                        'name' => 'ak_' . $key->getAttributeKeyHandle() . '_' . $name,
                        'type' => $column['type'],
                        'options' => $column['options'],
                    ];
                }
            }
        }

        $fromTable = $sm->listTableDetails($category->getIndexedSearchTable());
        $parser = new \Concrete\Core\Database\Schema\Parser\ArrayParser();
        $comparator = $this->comparator;

        if ($previousHandle != false) {
            foreach ($dropColumns as $column) {
                $toTable->dropColumn($column);
            }
        }

        $toTable = $parser->addColumns($toTable, $fields);
        $diff = $comparator->diffTable($fromTable, $toTable);
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
     * @param \Concrete\Core\Entity\Attribute\Key\Key $key
     * @param string|false $subKey the part of the name of a sub-field (if any - to be used for example if an attribute key needs multiple columns)
     *
     * @return string
     */
    protected function getIndexEntryColumn(Key $key, $subKey = false)
    {
        if ($subKey) {
            $column = sprintf('ak_%s_%s', $key->getAttributeKeyHandle(), $subKey);
        } else {
            $column = sprintf('ak_%s', $key->getAttributeKeyHandle());
        }

        return $column;
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
}
