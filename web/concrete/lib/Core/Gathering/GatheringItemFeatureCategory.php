<?
namespace Concrete\Core\Gathering;
use \Concrete\Core\Feature\Category as FeatureCategory;
class GatheringItemFeatureCategory extends FeatureCategory {

	public function assignmentIsInUse(FeatureAssignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(gaiID) as total from GatheringItemFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}
		
}
