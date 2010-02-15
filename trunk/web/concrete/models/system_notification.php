<?

class SystemNotification extends Object {

	const SN_TYPE_CORE_UPDATE = 10;
	const SN_TYPE_CORE_MESSAGE_HELP = 11;
	const SN_TYPE_CORE_MESSAGE_NEWS = 12;
	const SN_TYPE_CORE_MESSAGE_OTHER = 19;
	const SN_TYPE_ADDON_UPDATE = 20;
	const SN_TYPE_ADDON_MESSAGE = 22;
	
	public static function add($typeID, $text, $additionalText, $url) {
		$db = Loader::db();
		$date = Loader::helper('date')->getLocalDateTime();
		$db->Execute('insert into SystemNotifications (snTypeID, snNotificationText, snNotificationAdditionalText, snURL, snDatetime, snIsNew) values (?, ?, ?, ?, ?, ?)', array(
			$typeID, $text, $additionalText, $url, $date, 1
		));	
	}




}