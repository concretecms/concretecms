<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorPermissionKey extends PermissionKey {
	
	public function copyFromDefaultsToAggregator(PermissionKey $pk) {
		$db = Loader::db();
		$paID = $pk->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$db->Replace('AggregatorPermissionAssignments', array(
				'agID' => $this->permissionObject->getAggregatorID(), 
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
				),
				array('agID', 'pkID'), true);				
		}
	}


}	