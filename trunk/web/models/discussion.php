<?

class DiscussionModel extends Page {
	
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
		
	}
}