<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Schema\Schema;

class StandardSearchIndexer implements SearchIndexerInterface
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function isValid(CategoryInterface $category)
    {
        if (!($category instanceof StandardSearchIndexerInterface)) {
            throw new \Exception(t('Category %s must implement StandardSearchIndexerInterface.'), $category->getCategoryEntity()->getAttributeCategoryHandle());
        }

        return true;
    }

    protected function getIndexEntryColumn(AttributeKeyInterface $key, $subKey = false)
    {
        if ($subKey) {
            $column = sprintf('ak_%s_%s', $key->getAttributeKeyHandle(), $subKey);
        } else {
            $column = sprintf('ak_%s', $key->getAttributeKeyHandle());
        }

        return $column;
    }

    public function indexEntry(CategoryInterface $category, $mixed)
    {
        if ($this->isValid($category)) {
            // Clear the entry
            $details = $category->getSearchIndexFieldDefinition();
            $columnHeaders = array(
                $details['primary'][0] => $category->getIndexedSearchPrimaryKeyValue($mixed),
            );
            $this->connection->delete($category->getIndexedSearchTable(), $columnHeaders);

            // Regenerate based on values.
            $values = $category->getAttributeValues($mixed);

            /** @var \Doctrine\DBAL\Schema\Column[] $columns */
            $columns = $this->connection->getSchemaManager()->listTableColumns($category->getIndexedSearchTable());

            foreach ($values as $value) {
                $attributeValue = $value->getValueObject()->getSearchIndexValue();
                if (is_array($attributeValue)) {
                    foreach ($attributeValue as $key => $value) {
                        $col = $this->getIndexEntryColumn($value->getAttributeKey(), $key);
                        if (isset($columns[strtolower($col)])) {
                            $columnHeaders[$col] = $value;
                        }
                    }
                } else {
                    $col = $this->getIndexEntryColumn($value->getAttributeKey());
                    if (isset($columns[strtolower($col)])) {
                        $columnHeaders[$col] = $attributeValue;
                    }
                }
            }

            $this->connection->insert($category->getIndexedSearchTable(), $columnHeaders);
        }
    }

    public function createTable(CategoryInterface $category)
    {
        $schema = new Schema();
        if ($this->isValid($category)) {
            if (!$this->connection->tableExists($category->getIndexedSearchTable())) {
                $table = $schema->createTable($category->getIndexedSearchTable());
                $details = $category->getSearchIndexFieldDefinition();
                if (isset($details['columns'])) {
                    foreach ($details['columns'] as $column) {
                        $table->addColumn($column['name'], $column['type'], $column['options']);
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

    public function updateTable(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null)
    {
        if ($this->isValid($category)) {
            $controller = $key->getController();
            if ($key->getAttributeKeyHandle() == $previousHandle ||
                $key->isAttributeKeySearchable() == false ||
                $category->getIndexedSearchTable() == false ||
                $controller->getSearchIndexFieldDefinition() == false) {
                return false;
            }

            $fields = array();
            $dropColumns = array();
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
                    $fields[] = array(
                        'name' => 'ak_' . $key->getAttributeKeyHandle(),
                        'type' => $definition['type'],
                        'options' => $definition['options'],
                    );
                }
            } else {
                foreach ($definition as $name => $column) {
                    if (!$toTable->hasColumn('ak_' . $key->getAttributeKeyHandle() . '_' . $name)) {
                        $fields[] = array(
                            'name' => 'ak_' . $this->$key->getAttributeKeyHandle() . '_' . $name,
                            'type' => $column['type'],
                            'options' => $column['options'],
                        );
                    }
                }
            }

            $fromTable = $sm->listTableDetails($category->getIndexedSearchTable());
            $parser = new \Concrete\Core\Database\Schema\Parser\ArrayParser();
            $comparator = new \Doctrine\DBAL\Schema\Comparator();

            if ($previousHandle != false) {
                foreach ($dropColumns as $column) {
                    $toTable->dropColumn($column);
                }
            }

            $toTable = $parser->addColumns($toTable, $fields);
            $diff = $comparator->diffTable($fromTable, $toTable);
            if ($diff !== false) {
                $sql = $this->connection->getDatabasePlatform()->getAlterTableSQL($diff);
                $arr = array();
                foreach ($sql as $q) {
                    $arr[] = $q;
                    $this->connection->exec($q);
                }
            }
        }
    }
}
