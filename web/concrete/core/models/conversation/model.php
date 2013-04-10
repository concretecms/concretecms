<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation extends Object {

	public function getConversationID() {return $this->cnvID;}
	public function getConversationParentMessageID() {return $this->cnvParentMessageID;}
	
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
			if ($ui instanceof UserInfo) {
				$users[$ui->getUserID()] = $ui;
			}
		}
		return array_values($users);
	}

	public function setConversationParentMessageID($cnvParentMessageID) {
		$db = Loader::db();
		$db->Execute('update Conversations set cnvParentMessageID = ? where cnvID = ?', array($cnvParentMessageID, $this->getConversationID()));
		$this->cnvMessageParentID = $cnvParentMessageID;
	}

	public static function add() {
		$db = Loader::db();
		$date = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into Conversations (cnvDateCreated) values (?)', array($date));
		return Conversation::getByID($db->Insert_ID());
	}

	public function getConversationMessagesTotal() {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(cnvMessageID) from ConversationMessages where cnvID = ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1', array($this->cnvID));
		return $cnt;
	}
	

}
