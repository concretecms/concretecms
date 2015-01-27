<?php
namespace Concrete\Core\User\PrivateMessage;
use Loader;
use DateTime;
use Config;
class Limit {
	/**
	 * checks to see if a user has exceeded their limit for sending private messages
	 * @param int $uID
	 * @return boolean
	*/
	public function isOverLimit($uID){
		if(Config::get('concrete.user.private_messages.throttle_max') == 0) { return false; }
		if(Config::get('concrete.user.private_messages.throttle_max_timespan') == 0) { return false; }
		$db = Loader::db();
		$dt = new DateTime();
		$dt->modify('-'.Config::get('concrete.user.private_messages.throttle_max_timespan').' minutes');
		$v = array($uID, $dt->format('Y-m-d H:i:s'));
		$q = "SELECT COUNT(msgID) as sent_count FROM UserPrivateMessages WHERE uAuthorID = ? AND msgDateCreated >= ?";
		$count = $db->getOne($q,$v);

		if($count > Config::get('concrete.user.private_messages.throttle_max')) {
			self::notifyAdmin($uID);
			return true;
		} else {
			return false;
		}
	}

	public function getErrorObject() {
		$ve = Loader::helper('validation/error');
		$ve->add(t('You may not send more than %s messages in %s minutes', Config::get('concrete.user.private_messages.throttle_max'), Config::get('concrete.user.private_messages.throttle_max_timespan')));
		return $ve;
	}

	protected function notifyAdmin($offenderID) {
		$offender = UserInfo::getByID($offenderID);


		$ue = new \Concrete\Core\User\Event\UserInfo($offender);
		Events::dispatch('on_private_message_over_limit', $ue);

		$admin = UserInfo::getByID(USER_SUPER_ID);

		Log::addEntry(t("User: %s has tried to send more than %s private messages within %s minutes", $offender->getUserName(), Config::get('concrete.user.private_messages.throttle_max'), Config::get('concrete.user.private_messages.throttle_max_timespan')),t('warning'));

		Loader::helper('mail');
		$mh = new MailHelper();

		$mh->addParameter('offenderUname', $offender->getUserName());
		$mh->addParameter('profileURL', BASE_URL . View::url('/profile', 'view', $offender->getUserID()));
		$mh->addParameter('profilePreferencesURL', BASE_URL . View::url('/profile/edit'));

		$mh->to($admin->getUserEmail());
		$mh->addParameter('siteName', Config::get('concrete.site'));
		$mh->load('private_message_admin_warning');
		$mh->sendMail();
	}
}
