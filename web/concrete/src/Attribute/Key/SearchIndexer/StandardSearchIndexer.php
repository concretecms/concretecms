<?php
namespace Concrete\Core\Attribute\Key\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value;
use Doctrine\DBAL\Statement;

class StandardSearchIndexer implements SearchIndexerInterface
{

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
     * @param StandardSearchIndexerInterface $category
     * @param Key $key
     * @param $previousHandle
     */
    public function addSearchKey(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle)
    {
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
                        'name' => 'ak_' . $key->getAttributeKeyHandle() . '_' . $name,
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

    /**
     * @param StandardSearchIndexerInterface $category
     * @param Value $value
     * @param mixed $subject
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns($category->getIndexedSearchTable());

        $attributeValue = $value->getValueObject()->getSearchIndexValue();
        $details = $category->getSearchIndexFieldDefinition();
        $primary = $details['primary'][0];
        $primaryValue = $category->getIndexedSearchPrimaryKeyValue($subject);
        $columnValues = array();

        /**
         * @var $exists Statement
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

        $primaries = array($primary => $primaryValue);

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
