<?php
namespace Concrete\Core\Attribute\Key\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Types\Type;

class StandardSearchIndexer implements SearchIndexerInterface
{
    protected $connection;

    protected $comparator;

    public function __construct(Connection $connection, Comparator $comparator)
    {
        $this->connection = $connection;
        $this->comparator = $comparator;
    }

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
     * For certain fields (eg TEXT) Doctrine uses the length of the longest column to determine what field type to use.
     * For search indexing even if we may not currently have something long in a column,
     * we need the longest possible column so that we don't truncate any data.
     *
     * @param array $options
     *
     * @return array
     */
    private function setTypeLength($options)
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
     * Refresh the Search Index columns (if there are schema changes for example).
     *
     * @param CategoryInterface $category
     * @param AttributeKeyInterface $key
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
            $arr = [];
            foreach ($sql as $q) {
                $arr[] = $q;
                $this->connection->exec($q);
            }
        }
    }

    /**
     * @param StandardSearchIndexerInterface $category
     * @param Key $key
     * @param $previousHandle
     */
    public function updateSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null)
    {
        $controller = $key->getController();
        /*
         * Added this for some backward compatibility reason – but it's obviously not
         * right because it makes it so no search index columns get created.
        if (!$previousHandle) {
            $previousHandle = $key->getAttributeKeyHandle();
        }*/

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
            $arr = [];
            foreach ($sql as $q) {
                $arr[] = $q;
                $this->connection->exec($q);
            }
        }
    }

    /**
     * @param StandardSearchIndexerInterface $category
     * @param Value $value
     * @param mixed $subject
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
     * @param StandardSearchIndexerInterface $category
     * @param Value $value
     * @param mixed $subject
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns($category->getIndexedSearchTable());

        $attributeValue = $value->getSearchIndexValue();
        $details = $category->getSearchIndexFieldDefinition();
        $primary = $details['primary'][0];
        $primaryValue = $category->getIndexedSearchPrimaryKeyValue($subject);
        $columnValues = [];

        /**
         * @var Statement
         */
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
}
