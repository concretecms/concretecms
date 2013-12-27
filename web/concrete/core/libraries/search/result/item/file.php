<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_FileSearchResultItem extends SearchResultItem {

	public $fID;

	public function __construct(SearchResult $result, DatabaseItemListColumnSet $columns, $item) {
		parent::__construct($result, $columns, $item);
		$this->populateDetails($item);
	}

	protected function populateDetails($item) {
		$obj = $item->getJSONObject();
		foreach($obj as $key => $value) {
			$this->{$key} = $value;
		}
	}


}
