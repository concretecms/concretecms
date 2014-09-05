<?php
namespace Concrete\Core\File\Search\Result;
use \Concrete\Core\Search\Result\Item as SearchResultItem;
use \Concrete\Core\Search\Result\Result as SearchResult;
use \Concrete\Core\Search\Column\Set;

class Item extends SearchResultItem {

	public $fID;

	public function __construct(SearchResult $result, Set $columns, $item) {
		parent::__construct($result, $columns, $item);
		$this->populateDetails($item);
	}

	protected function populateDetails($item) {
		$obj = $item->getJSONObject();
		foreach($obj as $key => $value) {
			$this->{$key} = $value;
		}
		$this->isStarred = $item->isStarred();
	}


}
