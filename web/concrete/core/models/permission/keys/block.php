<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_BlockPermissionKey extends PermissionKey {

	public function copyFromPageOrAreaToBlock() {
		$paID = $this->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$co = $this->permissionObject->getBlockCollectionObject();
			$arHandle = $this->permissionObject->getAreaHandle();
			$db->Replace('BlockPermissionAssignments', array(
				'cID' => $co->getCollectionID(), 
				'cvID' => $co->getVersionID(), 
				'bID' => $this->permissionObject->getBlockID(), 
				'pkID' => $this->getPermissionKeyID(),
				'paID' => $paID), array('cID', 'cvID', 'bID', 'paID', 'pkID'), true);				
		}
	}
		

}
