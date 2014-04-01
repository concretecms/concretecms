<?
namespace Concrete\Core\User;
use \Concrete\Core\Search;
class UserSearchResult extends SearchResult {

	public function getItemDetails($item) {
		$node = new UserSearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new UserSearchResultColumn($this, $column);
		return $node;
	}

}
