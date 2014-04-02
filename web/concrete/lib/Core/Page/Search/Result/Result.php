<?
namespace Concrete\Core\Page\Search\Result;
use \Concrete\Core\Search\Result\Result as SearchResult;
class Result extends SearchResult {

	public function getItemDetails($item) {
		$node = new PageSearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new PageSearchResultColumn($this, $column);
		return $node;
	}

	public function getColumns() {
		if (!isset($this->columns)) {
			parent::getColumns();
			if ($this->getItemListObject()->isIndexedSearch()) {
				$column = new PageSearchResultColumn($this);
				$column->setColumnSortable(true);
				$column->setColumnKey('cIndexScore');
				$column->setColumnTitle(t('Score'));
				$column->setColumnStyleClass($this->getItemListObject()->getSearchResultsClass('cIndexScore'));
				$column->setColumnSortURL($this->getItemListObject()->getSortByURL('cIndexScore', 'desc', $this->getBaseURL()));
				array_unshift($this->columns, $column);
			}
		}
		return $this->columns;
	}


}
