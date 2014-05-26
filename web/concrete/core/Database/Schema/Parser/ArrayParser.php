<?php

namespace Concrete\Core\Database\Schema\Parser;

class ArrayParser {

	public function addColumns(\Doctrine\DBAL\Schema\Table $table, $columns) {
		foreach($columns as $column) {
            try {
                $field = $table->addColumn($column['name'], $column['type'], $column['options']);
            } catch( \Doctrine\DBAL\Schema\SchemaException $e) {
                //carry on... this field is probably already in the list
            }
		}
		return $table;
	}

	public function parse($definition, \Concrete\Core\Database\Connection $db) {
		$schema = new \Doctrine\DBAL\Schema\Schema();
		foreach($definition as $tableName => $details) {
			if ($db->tableExists($tableName)) {
				continue;
			}
			$table = $schema->createTable($tableName);
			$table = $this->addColumns($table, $details['columns']);
			$table->setPrimaryKey($details['primary']);
		}

		return $schema;

    }

}

