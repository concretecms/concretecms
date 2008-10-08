<?

class DiscussionModel extends Page {
	
	const CTHANDLE = 'discussion';
	const ATTRIBUTE_DISCUSSION_IS_POST_PINNED = "discussion_post_is_pinned";
	
	public function load($obj) {
		if ($obj instanceof Page) {
			$dm = DiscussionModel::getByID($obj->getCollectionID(), $obj->getVersionID());
			return $dm;
		}
	}
	
	public static function getByID($cID, $cvID = 'ACTIVE') {
		$where = "where Pages.cID = ?";
		$c = new DiscussionModel;
		$c->populatePage($cID, $where, $cvID);	
		
		$db = Loader::db();
		$row = $db->GetRow("select totalViews, lastPostCID, totalTopics, totalPosts, lastPostCID from DiscussionSummary where cID = ?", array($cID));
		
		$c->setTotalViews($row['totalViews']);
		$c->setTotalTopics($row['totalTopics']);
		$c->setTotalPosts($row['totalPosts']);
		$c->setLastPostCollectionID($row['lastPostCID']);
		return $c;
	}
	
	public function getDiscussions($cParentID) {
		$db = Loader::db();
		$discussions = array();
		$v = array(DiscussionModel::CTHANDLE, $this->getDiscussionCollectionID());
		$r = $db->Execute("select cID from Pages inner join PageTypes on Pages.ctID = PageTypes.ctID where PageTypes.ctHandle = ? and Pages.cParentID = ? order by Pages.cDisplayOrder asc", $v);				
		while ($row = $r->fetchRow()) {
			$discussions[] = DiscussionModel::getByID($row['cID'], 'ACTIVE');
		}
		return $discussions;
	}

	/** 
	 * Returns posts below this particular discussion
	 * @todo add paging
	 */
	public function getPosts() {
		Loader::model('discussion_post');
		$db = Loader::db();

		$posts = array();
		
		// first, we grab all "pinned" discussions
		$pinned = $db->GetCol("select p.cID from Pages p
			inner join Collections c on c.cID = p.cID
			inner join CollectionVersions cv on (p.cID = cv.cID and cv.cvIsApproved = 1)
			inner join CollectionAttributeValues cav on (p.cID = cav.cID and cav.cvID = cv.cvID)
			inner join CollectionAttributeKeys cak on (cak.akID = cav.akID)
			where cParentID = ? and cak.akHandle = ? and cav.value = 1 order by cDateAdded asc", array($this->getCollectionID(), DiscussionModel::ATTRIBUTE_DISCUSSION_IS_POST_PINNED));
		
		foreach($pinned as $o) {
			$posts[] = DiscussionPostModel::getByID($o);
		}
		
		$pinned[] = -1;
		$notin = implode(',', $pinned);

		// now we retrieve the posts, taking care not to include the pinned
		
		$v = array($notin, DiscussionPostModel::CTHANDLE, $this->getCollectionID());
		$r = $db->Execute("select Pages.cID from Pages inner join Collections on Collections.cID = Pages.cID inner join PageTypes on Pages.ctID = PageTypes.ctID where Pages.cID not in (?) and PageTypes.ctHandle = ? and cParentID = ? order by cDateAdded desc", $v);

		while ($row = $r->fetchRow()) {
			$posts[] = DiscussionPostModel::getByID($row['cID']);
		}
		return $posts;
	}
	
	/** 
	 * Adds a post to the system. Since this is a top level post it's the main "discussion" in a thread
	 * @todo - probably add the ability to specify the user ID, etc...
	 */
	public function addPost($subject, $message) {
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
	
	/** 
	 * Records the discussion as having been views
	 */
	public function recordView() {
		$db = Loader::db();
		$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'totalViews' => 'totalViews + 1'), 'cID', false);
	}
	
	public function incrementTotalPosts($num = 1) {
		$db = Loader::db();
		$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'totalPosts' => 'totalPosts + ' . $num), 'cID', false);
	}

	public function incrementTotalTopics($num = 1) {
		$db = Loader::db();
		$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'totalTopics' => 'totalTopics + ' . $num), 'cID', false);
	}

	public function decrementTotalPosts($num = 1) {
		$db = Loader::db();
		$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'totalPosts' => 'totalPosts - ' . $num), 'cID', false);
	}

	public function decrementTotalTopics($num = 1) {
		$db = Loader::db();
		$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'totalTopics' => 'totalTopics - '  . $num), 'cID', false);
	}
	
	public function updateLastPost($dpm) {
		$db = Loader::db();
		$db->Replace('DiscussionSummary', array('cID' => $this->getCollectionID(), 'lastPostCID' => $dpm->getCollectionID()), 'cID', false);
	}
	
	private function setTotalViews($totalViews) {$this->totalViews = $totalViews;}
	private function setTotalTopics($totalTopics) {$this->totalTopics = $totalTopics;}
	private function setTotalPosts($totalPosts) {$this->totalPosts = $totalPosts;}
	private function setLastPostCollectionID($lastPostCID) {$this->lastPostCID = $lastPostCID;}
	
	public function getTotalViews() {return $this->totalViews;}
	public function getTotalTopics() {return $this->totalTopics;}
	public function getTotalPosts() {return $this->totalPosts;}
	public function getLastPost() {
		if ($this->lastPostCID == 0) {
			return false;
		}
		$dpm = DiscussionPostModel::getByID($this->lastPostCID);
		if (!$dpm->isError()) {
			return $dpm;
		}
	}
	
}