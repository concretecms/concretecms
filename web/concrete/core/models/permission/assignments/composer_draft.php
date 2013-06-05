<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerDraftPermissionAssignment extends PermissionAssignment {
	
	protected $permissionObjectToCheck;
	
	protected $inheritedPermissions = array(
		'edit_composer_draft' => 'edit_composer_drafts_from_composer'
	);
	
	public function getPermissionAccessObject() {
		$db = Loader::db();
		if ($this->permissionObjectToCheck instanceof ComposerDraft) { 
 			$paID = $db->GetOne('select paID from ComposerDraftPermissionAssignments where cmpDraftID = ? and pkID = ?', array(
 			$this->permissionObject->getComposerDraftID(), $this->pk->getPermissionKeyID()
 			));
		} else if ($this->permissionObjectToCheck instanceof Composer && isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]));
			$paID = $db->GetOne('select paID from ComposerPermissionAssignments where cmpID = ? and pkID = ?', array(
				$this->permissionObjectToCheck->getComposerID(), $inheritedPKID
			));
		} else {
			return false;
		}
		
		if ($paID) {
			return PermissionAccess::getByID($paID, $this->pk);
		}
	}

	public function setPermissionObject(ComposerDraft $draft) {
		$this->permissionObject = $draft;
		if ($draft->overrideComposerPermissions()) {
			$this->permissionObjectToCheck = $draft;
		} else {
			$this->permissionObjectToCheck = $draft->getComposerObject();
		}
	}


	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update ComposerDraftPermissionAssignments set paID = 0 where pkID = ? and cmpDraftID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getComposerDraftID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('ComposerDraftPermissionAssignments', array('cmpDraftID' => $this->getPermissionObject()->getComposerDraftID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('cmpDraftID', 'pkID'), true);
		$pa->markAsInUse();
	}
		
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&cmpDraftID=' . $this->getPermissionObject()->getComposerDraftID();
	}

}
