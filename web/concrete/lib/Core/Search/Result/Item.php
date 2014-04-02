<?
namespace Concrete\Core\Search\Result;
class Item {

	public $columns = array();

	public function getColumns() {
		return $this->columns;
	}

	public function __construct(SearchResult $result, DatabaseItemListColumnSet $columns, $item) {
		foreach($columns->getColumns() as $col) {
			$o = new SearchResultItemColumn($col->getColumnKey(), $col->getColumnValue($item));
			$this->columns[] = $o;
		}
	}

}
