<?php
namespace Concrete\Core\User\Search\Result;
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
		$this->uID = $item->getUserID();
		$this->uName = $item->getUserName();
		$this->uEmail = $item->getUserEmail();
	}


}
