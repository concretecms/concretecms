<?
namespace Concrete\Core\File;
use \Concrete\Core\Search;
class FileSearchResult extends SearchResult {

	public function getItemDetails($item) {
		$node = new FileSearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getColumnDetails($column) {
		$node = new FileSearchResultColumn($this, $column);
		return $node;
	}

}
