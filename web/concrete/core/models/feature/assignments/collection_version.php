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


		
}
