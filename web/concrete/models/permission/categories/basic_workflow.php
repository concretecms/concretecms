<?
defined('C5_EXECUTE') or die("Access Denied.");
class BasicWorkflowPermissionKey extends WorkflowPermissionKey {
	
	public function getPermissionAccessID() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from BasicWorkflowPermissionAssignments where wfID = ? and pkID = ?', array(
 			$this->getPermissionObject()->getWorkflowID(), $this->getPermissionKeyID()
 		));
 		return $r;
	}
	
	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update BasicWorkflowPermissionAssignments set paID = 0 where pkID = ? and wfID = ?', array($this->pkID, $this->getPermissionObject()->getWorkflowID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('BasicWorkflowPermissionAssignments', array('wfID' => $this->getPermissionObject()->getWorkflowID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pkID), array('wfID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&wfID=' . $this->getPermissionObject()->getWorkflowID();
	}

}

class BasicWorkflowPermissionAccess extends PermissionAccess {

	
}
class BasicWorkflowPermissionAccessListItem extends PermissionAccessListItem {}