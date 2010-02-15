<?

class SystemNotification extends Object {

	const SN_TYPE_CORE_UPDATE = 10;
	const SN_TYPE_CORE_MESSAGE_HELP = 11;
	const SN_TYPE_CORE_MESSAGE_NEWS = 12;
	const SN_TYPE_CORE_MESSAGE_OTHER = 19;
	const SN_TYPE_ADDON_UPDATE = 20;
	const SN_TYPE_ADDON_MESSAGE = 22;
	
	public static function add($typeID, $title, $description, $body, $url) {
		$db = Loader::db();
		$date = Loader::helper('date')->getLocalDateTime();
		$db->Execute('insert into SystemNotifications (snTypeID, snTitle, snDescription, snBody, snURL, snDatetime, snIsNew) values (?, ?, ?, ?, ?, ?, ?)', array(
			$typeID, $title, $description, $body, $url, $date, 1
		));	
	}


	public static function addFromFeed($post, $type) {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(snID) from SystemNotifications where snURL = ?', array($post->get_permalink()));
		if ($cnt == 0) {
			// otherwise we already have this
			$db->Execute('insert into SystemNotifications (snTypeID, snTitle, snDescription, snBody, snURL, snDatetime, snIsNew) values (?, ?, ?, ?, ?, ?, ?)', array(
				$type, $post->get_title(), $post->get_description(), $post->get_content(), $post->get_permalink(), $post->get_date('Y-m-d H:i:s'), 1
			));
		}	
	}

	public static function getListByType($typeID) {

		
	}

}