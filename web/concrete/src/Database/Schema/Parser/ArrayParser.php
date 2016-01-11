<?php
namespace Concrete\Core\Database\Schema\Parser;

class ArrayParser
{
    public function addColumns(\Doctrine\DBAL\Schema\Table $table, $columns)
    {
        foreach ($columns as $column) {
            $table->addColumn($column['name'], $column['type'], $column['options']);
        }

        return $table;
    }

    public function parse($definition, \Concrete\Core\Database\Connection\Connection $db)
    {
        $schema = new \Doctrine\DBAL\Schema\Schema();
        foreach ($definition as $tableName => $details) {
            if ($db->tableExists($tableName)) {
                continue;
            }
            $table = $schema->createTable($tableName);
            if (isset($details['columns'])) {
                $table = $this->addColumns($table, $details['columns']);
            } else {
                throw new \Exception(t('Invalid column definition: %s in table %s', var_export($details, true), $tableName));
            }
            $table->setPrimaryKey($details['primary']);
        }

        return $schema;
    }
}
