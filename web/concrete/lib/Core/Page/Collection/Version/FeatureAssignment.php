<?
namespace Concrete\Core\Page\Collection\Version;
use \Concrete\Core\Feature\Assignment as FeatureAssignment;
class FeatureAssignment extends FeatureAssignment {

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

	public static function getFeature($feHandle, $page) {
		$db = Loader::db();
		$faID = $db->GetOne('select ca.faID from CollectionVersionFeatureAssignments ca inner join FeatureAssignments fa on ca.faID = fa.faID inner join Features fe on fa.feID = fe.feID where cID = ? and cvID = ? and fe.feHandle = ?', array(
			$page->getCollectionID(),
			$page->getVersionID(),
			$feHandle
		));
		if ($faID && $faID > 0) {
			$fa = FeatureAssignment::getByID($faID, $page);
			if (is_object($fa)) {
				return $fa;
			}
		}
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
