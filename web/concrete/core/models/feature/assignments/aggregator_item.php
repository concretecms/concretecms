<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_GatheringItemFeatureAssignment extends FeatureAssignment {

	protected $agiID;
	public function loadDetails($mixed) {
		$this->agiID = $mixed->getGatheringItemID();
	}

	public static function add(Feature $fe, FeatureDetail $fd, GatheringItem $item) {
		$fc = FeatureCategory::getByHandle('gathering_item');
		$fa = parent::add($fe, $fc, $fd, $item);
		$db = Loader::db();
		$db->Execute('insert into GatheringItemFeatureAssignments (agiID, faID) values (?, ?)', array(
			$item->getGatheringItemID(),
			$fa->getFeatureAssignmentID()
		));
		return $fa;
	}

	public static function getList($item) {
		$db = Loader::db();
		$r = $db->Execute('select faID from GatheringItemFeatureAssignments where agiID = ?', array(
			$item->getGatheringItemID()
		));
		$list = array();
		while ($row = $r->FetchRow()) {
			$fa = FeatureAssignment::getByID($row['faID'], $item);
			if (is_object($fa)) {
				$list[] = $fa;
			}
		}
		return $list;
	}

	public static function getFeature($feHandle, $item) {
		$db = Loader::db();
		$faID = $db->GetOne('select ca.faID from GatheringItemFeatureAssignments as inner join FeatureAssignments fa on as.faID = fa.faID inner join Features fe on fa.feID = fe.feID where agiID = ? and fe.feHandle = ?', array(
			$item->getGatheringItemID(), $feHandle
		));
		if ($faID && $faID > 0) {
			$fa = FeatureAssignment::getByID($faID, $item);
			if (is_object($fa)) {
				return $fa;
			}
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from GatheringItemFeatureAssignments where faID = ? and agiID = ?', array($this->getFeatureAssignmentID(), $this->agiID));
		if (!$this->assignmentIsInUse()) {
			parent::delete();
		}
	}


		
}
