<?
namespace Concrete\Core\Legacy;
use Loader;
/**
 * @deprecated
 */
class BlockRecord {

	protected $_table;

	public $bID;

	public function __construct($_table) {
		$this->_table = $_table;
	}

	public function load($where) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ' . $this->_table . ' where ' . $where);
		foreach($r as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function Replace() {
		$db = Loader::db();
		$data = array();
		foreach($this as $key => $value) {
			if (!in_array($key, array('_table'))) {
				$data[$key] = $value;
			}
		}
		$db->Replace($this->_table, $data, array('bID'));

	}


}