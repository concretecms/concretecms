<?php
namespace Concrete\Core\Feature\Category;
use Loader;
class CollectionVersionCategory extends Category {

	public function assignmentIsInUse(\Concrete\Core\Feature\Assignment\Assignment $fa) {
		$db = Loader::db();
		$num = $db->GetOne('select count(faID) from CollectionVersionFeatureAssignments where faID = ?', array($fa->getFeatureAssignmentID()));
		return $num > 0;
	}

}
