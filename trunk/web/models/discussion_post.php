<?

class DiscussionPostModel extends Page {
	
	private $body = false;
	private $userinfo = false;

	const CTHANDLE = 'discussion_post';
	
	public function load($obj) {
		if ($obj instanceof Page) {
			$dpm = DiscussionPostModel::getByID($obj->getCollectionID(), $obj->getVersionID());
			return $dpm;
		}
	}
	
	public function getSubject() { return $this->getCollectionName(); }
	public function getBody() { return $this->getCollectionDescription(); }
	public function getUserName() {
		if (is_object($this->userinfo)) {
			return  $this->userinfo->getUserName();
		} else {
			return "Anonymous";
		}
	}
	
	private function setUser($uID) {
		$this->userinfo = UserInfo::getByID($uID);
	}
	
	public static function getByID($cID, $cvID = 'ACTIVE') {
		$where = "where Pages.cID = ?";
		$c = new DiscussionPostModel;
		$c->populatePage($cID, $where, $cvID);		
		$c->setUser($c->getCollectionUserID());
		return $c;
	}

}