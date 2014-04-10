<?
namespace Concrete\Core\Search\Result;
use \Concrete\Core\Foundation\Collection\Database\Column\Set as DatabaseItemListColumnSet;
class Item {

	public $columns = array();

	public function getColumns() {
		return $this->columns;
	}

	public function __construct(Result $result, DatabaseItemListColumnSet $columns, $item) {
		foreach($columns->getColumns() as $col) {
			$o = new ItemColumn($col->getColumnKey(), $col->getColumnValue($item));
			$this->columns[] = $o;
		}
	}

}
