<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerPermissionKey extends PermissionKey {
	
	public function copyFromDefaultsToComposer(PermissionKey $pk) {
		$db = Loader::db();
		$paID = $pk->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$db->Replace('ComposerPermissionAssignments', array(
				'cmpID' => $this->permissionObject->getComposerID(), 
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
				),
				array('cmpID', 'pkID'), true);				
		}
	}


}	