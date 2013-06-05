<?

class Concrete5_Model_SystemNotification extends Object {

	const SN_TYPE_CORE_UPDATE = 10;
	const SN_TYPE_CORE_UPDATE_CRITICAL = 80;
	const SN_TYPE_CORE_MESSAGE_HELP = 11;
	const SN_TYPE_CORE_MESSAGE_NEWS = 12;
	const SN_TYPE_CORE_MESSAGE_OTHER = 19;
	const SN_TYPE_ADDON_UPDATE = 20;
	const SN_TYPE_ADDON_UPDATE_CRITICAL = 85;
	const SN_TYPE_ADDON_MESSAGE = 22;
	
	public function getSystemNotificationURL() {return $this->snURL;}
	public function getSystemNotificationAlternateURL() {return $this->snURL2;}
	public function getSystemNotificationTitle() {return $this->snTitle;}
	public function getSystemNotificationDescription() {return $this->snDescription;}
	public function getSystemNotificationBody() {return $this->snBody;}
	public function getSystemNotificationDateTime() {return $this->snDateTime;}
	public function isSystemNotificationNew() {return $this->snIsNew;}
	public function isSystemNotificationArchived() {return $this->snIsArchived;}
	public function getSystemNotificationTypeID() {return $this->snTypeID;}
	public function getSystemNotificationID() {return $this->snID;}

	public static function add($typeID, $title, $description, $body, $url, $url2 = null) {
		$db = Loader::db();
		$date = Loader::helper('date')->getLocalDateTime();
		$db->Execute('insert into SystemNotifications (snTypeID, snTitle, snDescription, snBody, snURL, snURL2, snDateTime, snIsNew) values (?, ?, ?, ?, ?, ?, ?, ?)', array(
			$typeID, $title, $description, $body, $url, $url2, $date, 1
		));	
	}


	public static function addFromFeed($post, $type) {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(snID) from SystemNotifications where snURL = ?', array($post->get_permalink()));
		if ($cnt == 0) {
			// otherwise we already have this
			$db->Execute('insert into SystemNotifications (snTypeID, snTitle, snDescription, snBody, snURL, snDateTime, snIsNew) values (?, ?, ?, ?, ?, ?, ?)', array(
				$type, $post->get_title(), $post->get_description(), $post->get_content(), $post->get_permalink(), $post->get_date('Y-m-d H:i:s'), 1
			));
		}	
	}
	
	public function markSystemNotificationAsRead() {
		$db = Loader::db();
		$db->Execute('update SystemNotifications set snIsNew = 0 where snID = ?', $this->snID);
	}
	
	public static function getByID($snID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from SystemNotifications where snID = ?', array($snID));
		if (is_array($row) && $row['snID']) {
			$sn = new SystemNotification();
			$sn->setPropertiesFromArray($row);
			return $sn;
		}
	}

}