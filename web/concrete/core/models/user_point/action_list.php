<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 

class Concrete5_Model_UserPointActionList extends DatabaseItemList {

	public function __construct() {
		$this->setBaseQuery();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT UserPointActions.*, Groups.gName FROM UserPointActions LEFT JOIN Groups ON Groups.gID = UserPointActions.gBadgeID');
	}

	
}