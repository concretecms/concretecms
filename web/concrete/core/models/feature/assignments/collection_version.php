<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_CollectionVersionFeatureAssignment extends FeatureAssignment {

	protected $cID;
	protected $cvID;

	public function loadDetails($page) {
		$this->cID = $page->getCollectionID();
		$this->cvID = $page->getVersionID();
	}

	public static function add(Feature $fe, FeatureDetail $fd, Collection $page) {
		$fc = FeatureCategory::getByHandle('collection_version');
		$fa = parent::add($fe, $fc, $fd, $page);
		$db = Loader::db();
		$db->Execute('insert into CollectionVersionFeatureAssignments (cID, cvID, faID) values (?, ?, ?)', array(
			$page->getCollectionID(),
			$page->getVersionID(),
			$fa->getFeatureAssignmentID()
		));
		return $fa;
	}

	public static function getList($page) {
		$db = Loader::db();
		$r = $db->Execute('select faID from CollectionVersionFeatureAssignments where cID = ? and cvID = ?', array(
			$page->getCollectionID(),
			$page->getVersionID()
		));
		$list = array();
		while ($row = $r->FetchRow()) {
			$fa = FeatureAssignment::getByID($row['faID'], $page);
			if (is_object($fa)) {
				$list[] = $fa;
			}
		}
		return $list;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from BlockFeatureAssignments where faID = ? and cID = ? and cvID = ?', array($this->getFeatureAssignmentID(), $this->cID, $this->cvID));
		$db->Execute('delete from CollectionVersionFeatureAssignments where faID = ? and cID = ? and cvID = ?', array($this->getFeatureAssignmentID(), $this->cID, $this->cvID));
		if (!$this->assignmentIsInUse()) {
			parent::delete();
		}
	}




		
}
