<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation extends Object {

	public function getConversationID() {return $this->cnvID;}
	
	public static function getByID($cnvID) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvID, cnvDateCreated from Conversations where cnvID = ?', array($cnvID));
		if (is_array($r) && $r['cnvID'] == $cnvID) {
			$cnv = new Conversation;
			$cnv->setPropertiesFromArray($r);
			return $cnv;
		}
	}

	public function getConversationMessageUsers() {
		$ml = new ConversationMessageList($this);
		$users = array();
		foreach ($ml->get() as $message) {
			$ui = $message->getConversationMessageUserObject();
			$users[$ui->getUserID()] = $ui;
		}
		return array_values($users);
	}

	public static function add() {
		$db = Loader::db();
		$date = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into Conversations (cnvDateCreated) values (?)', array($date));
		return Conversation::getByID($db->Insert_ID());
	}

	public function getConversationMessagesTotal() {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(cnvMessageID) from ConversationMessages where cnvID = ? and cnvIsMessageDeleted = 0', array($this->cnvID));
		return $cnt;
	}
	

	public function addMessage($cnvMessageSubject, $cnvMessageBody, $parentMessage = false, $user = false) {
		$db = Loader::db();
		$date = Loader::helper('date')->getSystemDateTime();
		$uID = 0;

		if (is_object($user)) {
			$ux = $user;
		} else {
			$ux = new User();
		}

		if ($ux->isRegistered()) {
			$uID = $ux->getUserID();
		}
		$cnvMessageParentID = 0;
		$cnvMessageLevel = 0;
		if (is_object($parentMessage)) {
			$cnvMessageParentID = $parentMessage->getConversationMessageID();
			$cnvMessageLevel = $parentMessage->getConversationMessageLevel() + 1;
		}

		$r = $db->Execute('insert into ConversationMessages (cnvMessageSubject, cnvMessageBody, cnvMessageDateCreated, cnvMessageParentID, cnvMessageLevel, cnvID, uID) values (?, ?, ?, ?, ?, ?, ?)', array($cnvMessageSubject, $cnvMessageBody, $date, $cnvMessageParentID, $cnvMessageLevel, $this->cnvID, $uID));
		return ConversationMessage::getByID($db->Insert_ID());
	}

}
