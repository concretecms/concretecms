<?
namespace Concrete\Core\User\PrivateMessage;
class Limit {
	/**
	 * checks to see if a user has exceeded their limit for sending private messages
	 * @param int $uID
	 * @return boolean
	*/
	public function isOverLimit($uID){
		if(USER_PRIVATE_MESSAGE_MAX == 0) { return false; }
		if(USER_PRIVATE_MESSAGE_MAX_TIME_SPAN == 0) { return false; }
		$db = Loader::db();
		$dt = new DateTime();
		$dt->modify('-'.USER_PRIVATE_MESSAGE_MAX_TIME_SPAN.' minutes');
		$v = array($uID, $dt->format('Y-m-d H:i:s'));
		$q = "SELECT COUNT(msgID) as sent_count FROM UserPrivateMessages WHERE uAuthorID = ? AND msgDateCreated >= ?";
		$count = $db->getOne($q,$v);
		
		if($count > USER_PRIVATE_MESSAGE_MAX) {
			self::notifyAdmin($uID);
			return true;
		} else {
			return false;
		}
	}
	
	public function getErrorObject() {
		$ve = Loader::helper('validation/error');
		$ve->add(t('You may not send more than %s messages in %s minutes', USER_PRIVATE_MESSAGE_MAX, USER_PRIVATE_MESSAGE_MAX_TIME_SPAN));
		return $ve;
	}
	
	protected function notifyAdmin($offenderID) {
		$offender = UserInfo::getByID($offenderID);
		Events::fire('on_private_message_over_limit', $offender);
		$admin = UserInfo::getByID(USER_SUPER_ID);
		
		Log::addEntry(t("User: %s has tried to send more than %s private messages within %s minutes", $offender->getUserName(), USER_PRIVATE_MESSAGE_MAX, USER_PRIVATE_MESSAGE_MAX_TIME_SPAN),t('warning'));
		
		Loader::helper('mail');
		$mh = new MailHelper();
		
		$mh->addParameter('offenderUname', $offender->getUserName());
		$mh->addParameter('profileURL', BASE_URL . View::url('/profile', 'view', $offender->getUserID()));
		$mh->addParameter('profilePreferencesURL', BASE_URL . View::url('/profile/edit'));
		
		$mh->to($admin->getUserEmail());
		$mh->load('private_message_admin_warning');
		$mh->sendMail();
	}
}