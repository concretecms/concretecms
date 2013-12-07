<?
/**
*
* @package Utilities
*/
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_UserSearchResult extends SearchResult {

	public function getItemDetails($item) {
		$node = new UserSearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new UserSearchResultColumn($this, $column);
		return $node;
	}

}
