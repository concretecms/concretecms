<?
namespace Concrete\Core\User;
use \Concrete\Core\Search;
class UserSearchResultItem extends SearchResultItem {

	public $fID;

	public function __construct(SearchResult $result, DatabaseItemListColumnSet $columns, $item) {
		parent::__construct($result, $columns, $item);
		$this->populateDetails($item);
	}

	protected function populateDetails($item) {
		$this->uID = $item->getUserID();
		$this->uName = $item->getUserName();
		$this->uEmail = $item->getUserEmail();
	}


}
