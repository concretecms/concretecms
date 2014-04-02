<?
namespace Concrete\Core\User\Point\Action;
use \Concrete\Core\Foundation\Collection\Database\DatabaseItemList;

class List extends DatabaseItemList {

	public function __construct() {
		$this->setBaseQuery();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT UserPointActions.*, Groups.gName FROM UserPointActions LEFT JOIN Groups ON Groups.gID = UserPointActions.gBadgeID');
	}

	public function filterByIsActive($active) {
		$this->filter('upaIsActive', $active);
	}
	
	
}