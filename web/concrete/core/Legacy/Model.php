<?
namespace Concrete\Core\Legacy;
use Loader;
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

	public function load($where) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ' . $this->_table . ' where ' . $where);
		foreach($r as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function Find($where) {
		$db = Loader::db();
		$return = array();
		$r = $db->GetAll('select * from ' . $this->_table . ' where ' . $where);
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
		$db->Replace($this->_table, $data, $primaryKeys);
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

	public function Insert() {
		$db = Loader::db();
		$data = array();

		foreach($this as $key => $value) {
			if (!in_array($key, array('_table'))) {
				$data[$key] = $value;
			}
		}
		$db->insert($this->_table, $data);

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