<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTypePermissionKey extends PermissionKey {
	
	public function copyFromDefaultsToPageType(PermissionKey $pk) {
		$db = Loader::db();
		$paID = $pk->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$db->Replace('PageTypePermissionAssignments', array(
				'ptID' => $this->permissionObject->getPageTypeID(), 
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
				),
				array('ptID', 'pkID'), true);				
		}
	}


}	