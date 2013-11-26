<?
/**
*
* @package Utilities
*/
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_FileSearchResult extends SearchResult {

	public function getItemDetails($item) {
		$node = new FileSearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new FileSearchResultColumn($this, $column);
		return $node;
	}

}
