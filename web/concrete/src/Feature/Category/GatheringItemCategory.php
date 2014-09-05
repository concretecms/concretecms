<?php
namespace Concrete\Core\Feature\Category;
use Loader;
class GatheringItemCategory extends Category {

	public function assignmentIsInUse(\Concrete\Core\Feature\Assignment\Assignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(gaiID) as total from GatheringItemFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}

}
