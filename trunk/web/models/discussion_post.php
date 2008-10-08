<?

class DiscussionPostModel extends Page {
	
	private $body = false;
	private $userinfo = false;
	private $replies = array();
	
	const CTHANDLE = 'discussion_post';
	
	public function load($obj) {
		if ($obj instanceof Page) {
			$dpm = DiscussionPostModel::getByID($obj->getCollectionID(), $obj->getVersionID());
			return $dpm;
		}
	}
	
	public function getTotalReplies() {
		return $this->totalReplies;	
	}
	
	public function getSubject() { return $this->getCollectionName(); }
	public function getBody() { return $this->getCollectionDescription(); }
	public function getReplyLevel() {return $this->replyLevel;}	
	public function isPostPinned() { return $this->getAttribute('discussion_post_is_pinned'); }
	public function getReplies() { return $this->replies;}
	
	public function addPostReply($subject, $message) {
		$u = new User();
		if ($this->canBePostedToBy($u)) {
			Loader::model('discussion_post');
			Loader::model('collection_types');
			$n1 = Loader::helper('text');
			
			$postType = CollectionType::getByHandle(DiscussionPostModel::CTHANDLE);
			$message = $n1->makenice($message);
			$data = array('cName' => $subject, 'cDescription' => $message);	
			$n = $this->add($postType, $data);
			// also add message to main content area
			$b1 = BlockType::getByHandle('content');
			$n->addBlock($b1, "Main", array('content' => $message));
			return DiscussionPostModel::getByID($n->getCollectionID(), 'ACTIVE');
		}
	}
	
	private function canBePostedToBy($u) {
		if (!$u->isRegistered()) {
			return false;
		}
		
		return true;
	}

	public function getUserName() {
		if (is_object($this->userinfo)) {
			return  $this->userinfo->getUserName();
		} else {
			return "Anonymous";
		}
	}
	public function getUserObject() {
		if (is_object($this->userinfo)) {
			return  $this->userinfo;
		}
	}
	
	private function setUser($uID) {
		$this->userinfo = UserInfo::getByID($uID);
	}
	
	/** 
	 * Returns all replies to a given topic
	 * @todo: paging?
	 */
	public function populateThreadedReplies($level = 0, $cID = 0) {
		if ($this->getNumChildren() == 0) {
			return;
		}
		if ($cID == 0) {
			$cID = $this->getCollectionID();
		}
		$db = Loader::db();
		$v = array($cID, DiscussionPostModel::CTHANDLE);
		$r = $db->Execute("select Pages.cID from Pages inner join Collections on Pages.cID = Collections.cID inner join PageTypes on Pages.ctID = PageTypes.ctID where cParentID = ? and ctHandle = ? order by cDateAdded asc", $v);
		while ($row = $r->fetchRow()) {
			$dpm = DiscussionPostModel::getByID($row['cID']);
			$dpm->setReplyLevel($level);
			$this->replies[] = $dpm;
			$this->populateThreadedReplies($level + 1, $row['cID']);
		}
	}
	
	public static function getByID($cID, $cvID = 'ACTIVE') {
		$where = "where Pages.cID = ?";
		$c = new DiscussionPostModel;
		$c->populatePage($cID, $where, $cvID);		
		$c->setUser($c->getCollectionUserID());
		
		$db = Loader::db();
		$row = $db->GetRow("select totalPosts from DiscussionSummary where cID = ?", array($cID));
		$c->setTotalReplies($row['totalPosts']);
		
		return $c;
	}
	
	/** 
	 * Goes up the tree until it finds the "discussion" this post lives under
	 */
	public function getDiscussion() {
		$db = Loader::db();
		$cParentID = $db->GetOne("select cParentID from Pages where cID = ?", array($this->getCollectionID()));
		$discussionID = 0;
		while ($cParentID > 0) {
			$ctHandle = $db->GetOne("select ctHandle from Pages inner join PageTypes on Pages.ctID = PageTypes.ctID where Pages.cID = ?", array($cParentID));
			if ($ctHandle == DiscussionModel::CTHANDLE) {
				$discussionID = $cParentID;
				$cParentID = 0;
			}	
			$cParentID = $db->GetOne("select cParentID from Pages where cID = ?", array($cParentID));
		}
		if ($discussionID > 0) {
			return DiscussionModel::getByID($discussionID);
		}
	}
	
	// iterates through and modifies the count all the way up the tree.
	// also, if this discussion post is immediately below the discussion page, it increments the totalTopics num
	public function updateParentCounts($num, $updateSelf = false) {
		$db = Loader::db();
		$cParentID = $this->getCollectionParentID();
		if ($num > 0) {
			$num = '+' . $num;
		}
		if ($updateSelf) {
			$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'totalPosts' => 'totalPosts ' . $num), 'cID', false);
		}
		$discussionID = 0;
		while ($cParentID > 0) {
			$db->Replace('DiscussionSummary', array('cID' => $cParentID, 'totalPosts' => 'totalPosts ' . $num), 'cID', false);
			$ctHandle = $db->GetOne("select ctHandle from Pages inner join PageTypes on Pages.ctID = PageTypes.ctID where Pages.cID = ?", array($cParentID));
			if ($ctHandle == DiscussionModel::CTHANDLE) {
				$discussionID = $cParentID;
				$cParentID = 0;
			}	
			$cParentID = $db->GetOne("select cParentID from Pages where cID = ?", array($cParentID));
		}
		
		if ($discussionID > 0 && $discussionID == $this->getCollectionParentID()) {
			$db->Replace('DiscussionSummary', array('cID' => $discussionID, 'totalTopics' => 'totalTopics ' . $num), 'cID', false);
		}
	}

	private function setTotalReplies($totalPosts) {$this->totalReplies = $totalPosts;}
	public function setReplyLevel($level) {$this->replyLevel = $level;}
	

}