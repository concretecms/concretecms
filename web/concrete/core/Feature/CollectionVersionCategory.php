<?
namespace Concrete\Core\Feature;
use Loader;
class CollectionVersionCategory extends Category {

	public function assignmentIsInUse(FeatureAssignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(faID) from CollectionVersionFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}
		
}
