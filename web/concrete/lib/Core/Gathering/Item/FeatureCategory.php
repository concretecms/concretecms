<?
namespace Concrete\Core\Gathering\Item
class FeatureCategory extends \Concrete\Core\Feature\Category {

	public function assignmentIsInUse(FeatureAssignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(gaiID) as total from GatheringItemFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}
		
}
