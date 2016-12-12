<?php
namespace Concrete\Core\Search\Result;
use \Concrete\Core\Search\Column\Set;
class Item {

	public $columns = array();

	public function getColumns() {
		return $this->columns;
	}

	public function __construct(Result $result, Set $columns, $item) {
		foreach($columns->getColumns() as $col) {
			$o = new ItemColumn($col->getColumnKey(), $col->getColumnValue($item));
			$this->columns[] = $o;
		}
	}

}
