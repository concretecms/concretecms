<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerPermissionAssignment extends PermissionAssignment {


	public function getPermissionAccessObject() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from ComposerPermissionAssignments where cmpID = ? and pkID = ?', array(
 			$this->permissionObject->getComposerID(), $this->pk->getPermissionKeyID()
 		));
 		return PermissionAccess::getByID($r, $this->pk);
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update ComposerPermissionAssignments set paID = 0 where pkID = ? and cmpID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getComposerID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('ComposerPermissionAssignments', array('cmpID' => $this->getPermissionObject()->getComposerID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('cmpID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&cmpID=' . $this->getPermissionObject()->getComposerID();
	}


}
