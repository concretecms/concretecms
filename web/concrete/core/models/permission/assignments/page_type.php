<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTypePermissionAssignment extends PermissionAssignment {


	public function getPermissionAccessObject() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from PageTypePermissionAssignments where cmpID = ? and pkID = ?', array(
 			$this->permissionObject->getPageTypeID(), $this->pk->getPermissionKeyID()
 		));
 		return PermissionAccess::getByID($r, $this->pk);
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update PageTypePermissionAssignments set paID = 0 where pkID = ? and cmpID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getPageTypeID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('PageTypePermissionAssignments', array('cmpID' => $this->getPermissionObject()->getPageTypeID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('cmpID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&cmpID=' . $this->getPermissionObject()->getPageTypeID();
	}


}
