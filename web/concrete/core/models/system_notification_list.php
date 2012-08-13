<?

class Concrete5_Model_SystemNotificationList extends DatabaseItemList {
	
	public function filterByType($type) {
		$db = Loader::db();
		$this->filter('sn.snTypeID', $type);
	}
	
	function __construct() {
		$this->setQuery("select sn.snID from SystemNotifications sn");
		$this->sortBy('snDateTime', 'desc');
	}

	public function get($itemsToGet = 0, $offset = 0) {
		$r = parent::get($itemsToGet, $offset);
		$posts = array();
		foreach($r as $row) {
			$sn = SystemNotification::getByID($row['snID']);
			if (is_object($sn)) {
				$posts[] = $sn;
			}
		}
		return $posts;
	}
	
}
