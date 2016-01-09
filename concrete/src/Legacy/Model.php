<?php
namespace Concrete\Core\Legacy;

/**
 * @deprecated
 */
class Model {

	protected $_table;

	public function __construct($_table = false) {
		if ($_table) {
			$this->_table = $_table;
		}
	}

	public function load($where, $args = array()) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ' . $this->_table . ' where ' . $where, $args);
		foreach($r as $key => $value) {
			$this->{$key} = $value;
		}
		if ($r && count(array_keys($r) > 0)) {
			return true;
		}
		return false;
	}

	public function Find($where, $args = array()) {
		$db = Loader::db();
		$return = array();
		$r = $db->GetAll('select * from ' . $this->_table . ' where ' . $where, $args);
		foreach($r as $row) {
			$o = new $this;
			$o = array_to_object($o, $row);
			$return[] = $o;
		}
		return $return;
	}

	public function Save() {
		return $this->Replace();
	}

	public function Replace() {
		$db = Loader::db();
		$data = array();

		$primaryKeys = $this->getPrimaryKeys();

		foreach($this as $key => $value) {
			if (!in_array($key, array('_table'))) {
				$data[$key] = $value;
			}
		}

		$columnName = $this->getAutoIncrementColumnName();
		$db->Replace($this->_table, $data, $primaryKeys);
		$this->setAutoincrementColumn($columnName);
		return 1;
	}

	protected function getPrimaryKeys() {
		$db = Loader::db();
		$primaryKeys = array();
		$sm = $db->getSchemaManager();
		$details = $sm->listTableDetails($this->_table);
		$index = $details->getPrimaryKey();
		$columns = $index->getColumns();
		foreach($columns as $column) {
			$primaryKeys[] = $column;
		}
		return $primaryKeys;
	}

	protected function getAutoIncrementColumnName() {
		$db = Loader::db();
		$sm = $db->getSchemaManager();
		$details = $sm->listTableDetails($this->_table);
		foreach($details->getColumns() as $name => $column) {
			if($column->getAutoincrement()) {
				return $name;
			}
		}
		return null;
	}
	
	protected function setAutoIncrementColumn($name) {
		if(empty($name)) {
			return;
		}

		if(property_exists($this, $name) && empty($this->$name)) {
			$db = Loader::db();
			$this->$name = $db->lastInsertId();
		}
	}

	public function Insert() {
		$db = Loader::db();
		$data = array();

		foreach($this as $key => $value) {
			if (!in_array($key, array('_table'))) {
				$data[$key] = $value;
			}
		}

		$columnName = $this->getAutoIncrementColumnName();
		$db->insert($this->_table, $data);
		$this->setAutoincrementColumn($columnName);
	}

	public function Delete() {
		if ($this->_table) {
			$db = Loader::db();
			$primaryKeys = $this->getPrimaryKeys();
			$data = array();
			foreach($primaryKeys as $key) {
				$data[$key] = $this->{$key};
			}
			$db->delete($this->_table, $data);
		}
	}

}
