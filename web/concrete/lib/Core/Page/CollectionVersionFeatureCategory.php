<?
namespace Concrete\Core\Page;
use \Concrete\Core\Feature\Category as FeatureCategory;
class CollectionVersionFeatureCategory extends FeatureCategory {

	public function assignmentIsInUse(FeatureAssignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(faID) from CollectionVersionFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}
		
}
