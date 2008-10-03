<?

class DiscussionModel extends Page {
	
	const CTHANDLE = 'discussion';
	public function load($obj) {
		if ($obj instanceof Page) {
			$dm = DiscussionModel::getByID($obj->getCollectionID(), $obj->getVersionID());
			return $dm;
		}
	}
	
	public static function getByID($cID, $cvID) {
		$where = "where Pages.cID = ?";
		$c = new DiscussionModel;
		$c->populatePage($cID, $where, $cvID);	
		return $c;
	}

	/** 
	 * Returns posts below this particular discussion
	 * @todo add paging
	 */
	public function getPosts() {
		Loader::model('discussion_post');
		$db = Loader::db();
		$v = array(DiscussionPostModel::CTHANDLE, $this->getCollectionID());
		$r = $db->Execute("select Pages.cID from Pages inner join Collections on Collections.cID = Pages.cID inner join PageTypes on Pages.ctID = PageTypes.ctID where PageTypes.ctHandle = ? and cParentID = ? order by cDateAdded desc", $v);
		$posts = array();
		while ($row = $r->fetchRow()) {
			$posts[] = DiscussionPostModel::getByID($row['cID']);
		}
		return $posts;
	}
}