<?php
namespace Concrete\Core\File\Search\Result;
use \Concrete\Core\Search\Result\Result as SearchResult;
use Loader;
class Result extends SearchResult {

	public function getItemDetails($item) {
		$node = new Item($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new Column($this, $column);
		return $node;
	}

}
