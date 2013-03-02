<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_CollectionVersionFeatureAssignment extends FeatureAssignment {

	public function loadDetails() {
			
	}

	public static function add(FeatureDetail $fd, Collection $page) {
		$fc = FeatureCategory::getByHandle('collection_version');
		$fa = parent::add($fc, $fd);
		$db = Loader::db();
		$db->Execute('insert into CollectionVersionFeatureAssignments (cID, cvID, faID) values (?, ?, ?)', array(
			$page->getCollectionID(),
			$page->getVersionID(),
			$fa->getFeatureAssignmentID()
		));
	}

	public static function getList($page) {
		$db = Loader::db();
		$r = $db->Execute('select faID from CollectionVersionFeatureAssignments where cID = ? and cvID = ?', array(
			$page->getCollectionID(),
			$page->getVersionID()
		));
		$list = array();
		while ($row = $r->FetchRow()) {
			$fa = FeatureAssignment::getByID($row['faID']);
			if (is_object($fa)) {
				$list[] = $fa;
			}
		}
		return $list;
	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from CollectionVersionFeatureAssignments where faID = ?', array($this->getFeatureAssignmentID()));
	}




		
}
