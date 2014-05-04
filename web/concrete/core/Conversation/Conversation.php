<?
namespace Concrete\Core\Conversation;
use Loader;
use \Concrete\Core\Foundation\Object;
use Page;
use \Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;

class Conversation extends Object {

	public function getConversationID() {return $this->cnvID;}
	public function getConversationParentMessageID() {return $this->cnvParentMessageID;}
	public function getConversationDateCreated() {return $this->cnvDateCreated;}
	public function getConversationDateLastMessage() {return $this->cnvDateLastMessage;}
	public function getConversationMessagesTotal() {return $this->cnvMessagesTotal;}

	public static function getByID($cnvID) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvID, cID, cnvDateCreated, cnvDateLastMessage, cnvMessagesTotal from Conversations where cnvID = ?', array($cnvID));
		if (is_array($r) && $r['cnvID'] == $cnvID) {
			$cnv = new static;
			$cnv->setPropertiesFromArray($r);
			return $cnv;
		}
	}

	public function getConversationPageObject() {
		if ($this->cID) {
			$c = Page::getByID($this->cID, 'ACTIVE');
			if (is_object($c) && !$c->isError()) {
				return $c;
			}
		}
	}

	public function setConversationPageObject($c) {
		$db = Loader::db();
		$db->Execute('update Conversations set cID = ? where cnvID = ?', array($c->getCollectionID(), $this->getConversationID()));
		$this->cID = $c->getCollectionID();
	}

	public function updateConversationSummary() {
		$db = Loader::db();
		$date = $db->GetOne('select max(cnvMessageDateCreated) from ConversationMessages where cnvID =  ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1', array(
			$this->getConversationID()
		));
		if (!$date) {
			$date = $this->getConversationDateCreated();
		}

		$total = $db->GetOne('select count(cnvMessageID) from ConversationMessages where cnvID = ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1', array($this->cnvID));
		$db->Execute('update Conversations set cnvDateLastMessage = ?, cnvMessagesTotal = ? where cnvID = ?', array(
			$date, $total, $this->getConversationID()
		));
	}

	public function getConversationMessageUsers() {
		$ml = new ConversationMessageList();
		$ml->filterByConversation($this);
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
		$r = $db->Execute('insert into Conversations (cnvDateCreated, cnvDateLastMessage) values (?, ?)', array($date, $date));
		return static::getByID($db->Insert_ID());
	}


}
