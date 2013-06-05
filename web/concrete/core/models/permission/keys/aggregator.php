<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_GatheringPermissionKey extends PermissionKey {
	
	public function copyFromDefaultsToGathering(PermissionKey $pk) {
		$db = Loader::db();
		$paID = $pk->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$db->Replace('GatheringPermissionAssignments', array(
				'gaID' => $this->permissionObject->getGatheringID(), 
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
				),
				array('gaID', 'pkID'), true);				
		}
	}


}	
