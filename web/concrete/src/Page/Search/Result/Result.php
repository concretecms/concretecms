<?php
namespace Concrete\Core\Page\Search\Result;
use Concrete\Core\Search\Column\Column as BaseColumn;
use Loader;
use \Concrete\Core\Search\Result\Result as SearchResult;
class Result extends SearchResult {

	public function getItemDetails($item) {
		$node = new Item($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new Column($this, $column);
		return $node;
	}

	public function getColumns() {
		if (!isset($this->columns)) {
			parent::getColumns();
			if ($this->getItemListObject()->isFulltextSearch()) {
                $baseColumn = new BaseColumn('cIndexScore', t('Score'), false, true, 'desc');
				$column = new Column($this, $baseColumn);
				array_unshift($this->columns, $column);
			}
		}
		return $this->columns;
	}


}
