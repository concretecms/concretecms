<?php

namespace Concrete\Core\Database\Schema\Parser;

class Legacy extends Parser {

	/** 
	 * Transforms the XML from Adodb XML into 
	 * Doctrine DBAL Schema
	 */
	public function parse() {
		$x = $this->rawXML;
		$schema = new \Doctrine\DBAL\Schema\Schema();
		foreach($x->table as $t) {
			$table = $schema->createTable((string) $t['name']);
			foreach($t->field as $f) {
				$options = $this->_getColumnOptions($f);
				$field = $table->addColumn((string) $f['name'], $this->_getColumnType($f), $options);				
			}
			$this->_setPrimaryKeys($t, $table);
			$this->_setIndexes($t, $table);
			$this->_setTableOpts($t, $table);
		}
		return $schema;
	}

	protected function _setTableOpts(\SimpleXMLElement $table, $schemaTable) {
		if ($table->opt) {
			$opt = $table->opt->__toString();
			 if ($opt == 'ENGINE=MYISAM') {
			 	$schemaTable->addOption('engine', 'MYISAM');
			 }
		}
	}

	protected function _setPrimaryKeys(\SimpleXMLElement $table, $schemaTable) {
		$primaryKeys = array();
		foreach($table->field as $column) {
			if ($column->autoincrement || $column->AUTOINCREMENT || $column->key || $column->KEY) {
				$primaryKeys[] = (string) $column['name'];
			}
		}	
		if (count($primaryKeys) > 0) {
			$schemaTable->setPrimaryKey($primaryKeys);
		}
	}	

	protected function _setIndexes(\SimpleXMLElement $table, $schemaTable) {
		foreach($table->index as $index) {
			$name = (string) $index['name'];
			$fields = array();
			$flags = array();
			if ($index->UNIQUE || $index->unique) {
				$fields[] = $index->col->__toString();
				$schemaTable->addUniqueIndex($fields, $name);
			} else {
				foreach($index->col as $col) {
					$fields[] = $col->__toString();
				}
				if ($index->fulltext || $index->FULLTEXT) {
					$flags[] = 'FULLTEXT';
				}
				$schemaTable->addIndex($fields, $name, $flags);
			}
		}	
	}	


	protected function _getColumnOptions(\SimpleXMLElement $column) {
		$type = (string) $column['type'];
		$size = (string) $column['size'];
		$options = array();
		if ($size) {
			$options['length'] = $size;
		}
		if ($column->unsigned || $column->UNSIGNED) {
			$options['unsigned'] = true;
		}
		if ($column->default) {
			if (isset($column->default['value'])) {
				$options['default'] = (string) $column->default['value'];
			}
			if (isset($column->default['VALUE'])) {
				$options['default'] = (string) $column->default['VALUE'];
			}			
		}
		if ($column->DEFAULT) {
			if (isset($column->DEFAULT['value'])) {
				$options['default'] = (string) $column->DEFAULT['value'];
			}
			if (isset($column->DEFAULT['VALUE'])) {
				$options['default'] = (string) $column->DEFAULT['VALUE'];
			}			
		}
		if ($column->notnull || $column->NOTNULL) {
			$options['notnull'] = true;
		} else {
			$options['notnull'] = false;
		}
		if ($column->autoincrement || $column->AUTOINCREMENT) {
			$options['autoincrement'] = true;
		}
		return $options;
	}

	protected function _getColumnType(\SimpleXMLElement $column) {
		$type = (string) $column['type'];
		$size = (string) $column['size'];
		if ($type == 'I') {
			return 'integer';
		}
		if ($type == 'I8') {
			return 'bigint';
		}
		if ($type == 'I1') {
			return 'boolean';
		}
		if ($type == 'I2') {
			return 'smallint';
		}
		if ($type == 'C') {
			return 'string';
		}
		if ($type == 'X') {
			return 'text';
		}
		if ($type == 'X2') {
			return 'text';
		}
		if ($type == 'T') {
			return 'datetime';
		}
		if ($type == 'D') {
			return 'date';
		}
		if ($type == 'N') {
			return 'decimal';
		}
	}

}

