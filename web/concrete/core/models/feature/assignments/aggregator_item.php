<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorItemFeatureAssignment extends FeatureAssignment {

	protected $agiID;
	public function loadDetails($mixed) {
		$this->agiID = $mixed->getAggregatorItemID();
	}

	public static function add(Feature $fe, FeatureDetail $fd, AggregatorItem $item) {
		$fc = FeatureCategory::getByHandle('aggregator_item');
		$fa = parent::add($fe, $fc, $fd, $item);
		$db = Loader::db();
		$db->Execute('insert into AggregatorItemFeatureAssignments (agiID, faID) values (?, ?)', array(
			$item->getAggregatorItemID(),
			$fa->getFeatureAssignmentID()
		));
		return $fa;
	}

	public static function getList($item) {
		$db = Loader::db();
		$r = $db->Execute('select faID from AggregatorItemFeatureAssignments where agiID = ?', array(
			$item->getAggregatorItemID()
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
		$faID = $db->GetOne('select ca.faID from AggregatorItemFeatureAssignments as inner join FeatureAssignments fa on as.faID = fa.faID inner join Features fe on fa.feID = fe.feID where agiID = ? and fe.feHandle = ?', array(
			$item->getAggregatorItemID(), $feHandle
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
		$db->Execute('delete from AggregatorItemFeatureAssignments where faID = ? and agiID = ?', array($this->getFeatureAssignmentID(), $this->agiID));
		if (!$this->assignmentIsInUse()) {
			parent::delete();
		}
	}


		
}
