<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 

class Concrete5_Model_UserPointEntryList extends DatabaseItemList {

	protected $autoSortColumns = array('uName', 'upaName', 'upPoints', 'timestamp');

	public function __construct() {
		$this->setBaseQuery();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT UserPointHistory.upID
			FROM UserPointHistory
			LEFT JOIN UserPointActions ON UserPointActions.upaID = UserPointHistory.upaID
			LEFT JOIN Groups ON UserPointActions.gBadgeID = Groups.gID
			LEFT JOIN Users ON UserPointHistory.upuID = Users.uID
		');
	}

	
	/**
	 * @param int $gID
	 * @return void
	 */
	public function filterByGroupID($gID) {
		$this->filter('UserPointActions.gBadgeID',$gID);	
	}
	
	/**
	 * @param string $uName
	 * @return void
	 */
	public function filterByUserName($uName) {
		$this->filter('Users.uName',$uName);	
	}

	
	public function filterByUserPointActionName($upaName) {
		$db = Loader::db();
		$this->filter(false,"UserPointActions.upaName LIKE ".$db->quote('%'.$upaName.'%'));
	}

	/**
	 * @param int $uID
	 * @return void
	 */
	public function filterByUserID($uID) {
		$this->filter('UserPointHistory.upuID',$upaTypeID);
	}

	public function get($items = 0, $offset = 0) {
		$resp = parent::get($items, $offset);
		$entries = array();
		foreach($resp as $r) {
			$up = new UserPointEntry();
			$up->load($r['upID']);
			$entries[] = $up;
		}
		return $entries;
	}
	
}