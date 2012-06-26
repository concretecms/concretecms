<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AreaPermissionKey extends PermissionKey {
	
	public function copyFromPageToArea() {
		$db = Loader::db();
		$paID = $this->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$db->Replace('AreaPermissionAssignments', array(
				'cID' => $this->permissionObject->getCollectionID(), 
				'arHandle' => $this->permissionObject->getAreaHandle(), 
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
				),
				array('cID', 'arHandle', 'pkID'), true);				
		}
	}
	
	


}
