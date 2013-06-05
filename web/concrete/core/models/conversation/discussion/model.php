<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ConversationDiscussion extends Object {

	public static function add(Page $c) {
		$db = Loader::db();
		if (!$ctID) {
			$ctID = 0;
		}
		$cID = $c->getCollectionID();
		$date = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into ConversationDiscussions (cnvDiscussionDateCreated, cID) values (?, ?)', array($date, $cID));
		return ConversationDiscussion::getByID($db->Insert_ID());
	}

	public function getConversationDiscussionCollectionObject() {
		$c = Page::getByID($this->cID);
		if (is_object($c) && !$c->isError()) {
			return $c;
		}
	}

	public function getConversationDiscussionID() {return $this->cnvDiscussionID;}
	public function getConversationDiscussionCollectionTypeID() {return $this->ctID;}
	public function getConversationDiscussionCollectionID() {return $this->cID;}

	public function getConversationDiscussionDateTime() {
		return $this->cnvDiscussionDateCreated;
	}
	public function getConversationDiscussionDateTimeOutput() {
		return t('Posted on %s', Loader::helper('date')->date('F d, Y \a\t g:i a', strtotime($this->cnvDiscussionDateCreated)));
	}

	public static function getByID($cnvDiscussionID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ConversationDiscussions where cnvDiscussionID = ?', array($cnvDiscussionID));
		if (is_array($r) && $r['cnvDiscussionID'] == $cnvDiscussionID) {
			$d = new ConversationDiscussion;
			$d->setPropertiesFromArray($r);
			return $d;
		}
	}
}
