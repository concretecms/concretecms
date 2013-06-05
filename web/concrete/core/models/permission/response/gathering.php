<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_GatheringPermissionResponse extends PermissionResponse {
	
	public function canEditGatheringItems() {
		// Eventually this will be overrideable at a particular gathering level.
		$tp = PermissionKey::getByHandle('edit_gatherings');
		return $tp->can();
	}

}
