<?
namespace Concrete\Core\User\Result;
use \Concrete\Core\Search\Result as SearchResult;
class Result extends SearchResult {

	public function getItemDetails($item) {
		$node = new UserSearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new UserSearchResultColumn($this, $column);
		return $node;
	}

}
