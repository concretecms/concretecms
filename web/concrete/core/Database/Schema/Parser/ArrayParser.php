<?php

namespace Concrete\Core\Database\Schema\Parser;

class ArrayParser {

	public function addColumns(\Doctrine\DBAL\Schema\Table $table, $columns) {
		foreach($columns as $column) {
			$field = $table->addColumn($column['name'], $column['type'], $column['options']);				
		}
		return $table;
	}

	public function parse($definition, \Concrete\Core\Database\Connection $db) {
		$tables = $db->MetaTables();
		$schema = new \Doctrine\DBAL\Schema\Schema();
		foreach($definition as $table => $details) {
			if (in_array($table, $tables)) {
				continue;
			}
			$table = $schema->createTable($table);
			$table = $this->addColumns($table, $details['columns']);
			$table->setPrimaryKey($details['primary']);
		}

		return $schema;

    }

}

