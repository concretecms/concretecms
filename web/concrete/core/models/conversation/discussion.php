<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ConversationDiscussion extends Object {

	public static function add(Page $c, $ctID = false) {
		$db = Loader::db();
		if (!$ctID) {
			$ctID = 0;
		}
		$cID = $c->getCollectionID();
		$date = Loader::helper('date')->getSystemDateTime();
		$r = $db->Execute('insert into ConversationDiscussions (cnvDiscussionDateCreated, cID, ctID) values (?, ?, ?)', array($date, $cID, $ctID));
		return ConversationDiscussion::getByID($db->Insert_ID());
	}

	public function getConversationDiscussionCollectionObject() {
		$c = Page::getByID($this->cID);
		if (is_object($c) && !$c->isError()) {
			return $c;
		}
	}

	public function getConversationDiscussionCollectionTypeObject() {
		$ct = CollectionType::getByID($this->ctID);
		return $ct;
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

	public function setConversationDiscussionCollectionTypeID($ctID) {
		$db = Loader::db();
		if (!$ctID) {
			$ctID = 0;
		}
		$db->execute('UPDATE ConversationDiscussions SET ctID = ? WHERE cnvDiscussionID = ?',array($ctID, $this->cnvDiscussionID));
		$this->ctID = $ctID;
	}

	/** 
	 * Creates a page of this type below the current page. Adds the relevant blocks to that page.
	 */
	public function addConversation($cnvDiscussionSubject, $cnvMessageBody) {
		$ct = $this->getConversationDiscussionCollectionTypeObject();
		$c = $this->getConversationDiscussionCollectionObject();
		$args['cName'] = $cnvDiscussionSubject;
		$conversation = $c->add($ct, $args);
		$bt = BlockType::getByHandle(BLOCK_HANDLE_CONVERSATION);
		$bargs = array(
			'displayMode' => 'threaded',
			'enablePosting' => 1,
			'paginate' => 1,
			'itemsPerPage' => 50,
			'orderBy' => 'date_desc',
			'enableOrdering' => true,
			'enableCommentRating' => true,
			'displayPostingForm' => true, 
			'insertNewMessages' => 'top'
		);
		$conversation->addBlock($bt, 'Main', $bargs);
		return $conversation;
	}

}
