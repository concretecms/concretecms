<?php

namespace Concrete\Core\Database\Schema\Parser;

class ArrayParser {

	protected $definition = array();

	public function __construct($definition) {
		$this->definition = $definition;
	}

	public function parse(\Concrete\Core\Database\Connection $db) {
		$tables = $db->MetaTables();
		$schema = new \Doctrine\DBAL\Schema\Schema();
		foreach($this->definition as $table => $details) {
			if (in_array($table, $tables)) {
				continue;
			}
			$table = $schema->createTable($table);
			foreach($details['columns'] as $column) {
				$field = $table->addColumn($column['name'], $column['type'], $column['options']);				
			}
			$table->setPrimaryKey($details['primary']);
		}

		return $schema;

    }

}

