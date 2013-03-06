<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorItemFeatureAssignment extends FeatureAssignment {

	public function loadDetails() {
			
	}

	public static function add(Feature $fe, FeatureDetail $fd, AggregatorItem $item) {
		$fc = FeatureCategory::getByHandle('aggregator_item');
		$fa = parent::add($fe, $fc, $fd);
		$db = Loader::db();
		$db->Execute('insert into AggregatorItemFeatureAssignments (agiID, faID) values (?, ?)', array(
			$item->getAggregatorItemID(),
			$fa->getFeatureAssignmentID()
		));
	}

	public static function getList($item) {
		$db = Loader::db();
		$r = $db->Execute('select faID from AggregatorItemFeatureAssignments where agiID = ?', array(
			$item->getAggregatorItemID()
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
		$db->Execute('delete from AggregatorItemFeatureAssignments where faID = ?', array($this->getFeatureAssignmentID()));
	}


		
}
