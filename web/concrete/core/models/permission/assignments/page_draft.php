<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageDraftPermissionAssignment extends PermissionAssignment {
	
	protected $permissionObjectToCheck;
	
	protected $inheritedPermissions = array(
		'edit_page_draft' => 'edit_page_drafts_from_composer'
	);
	
	public function getPermissionAccessObject() {
		$db = Loader::db();
		if ($this->permissionObjectToCheck instanceof PageDraft) { 
 			$paID = $db->GetOne('select paID from PageDraftPermissionAssignments where pDraftID = ? and pkID = ?', array(
 			$this->permissionObject->getPageDraftID(), $this->pk->getPermissionKeyID()
 			));
		} else if ($this->permissionObjectToCheck instanceof PageType && isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]));
			$paID = $db->GetOne('select paID from PageTypePermissionAssignments where ptID = ? and pkID = ?', array(
				$this->permissionObjectToCheck->getPageTypeID(), $inheritedPKID
			));
		} else {
			return false;
		}
		
		if ($paID) {
			return PermissionAccess::getByID($paID, $this->pk);
		}
	}

	public function setPermissionObject(PageDraft $draft) {
		$this->permissionObject = $draft;
		if ($draft->overridePageTypePermissions()) {
			$this->permissionObjectToCheck = $draft;
		} else {
			$this->permissionObjectToCheck = $draft->getPageTypeObject();
		}
	}


	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update PageDraftPermissionAssignments set paID = 0 where pkID = ? and pDraftID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getPageDraftID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('PageDraftPermissionAssignments', array('pDraftID' => $this->getPermissionObject()->getPageDraftID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('pDraftID', 'pkID'), true);
		$pa->markAsInUse();
	}
		
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&pDraftID=' . $this->getPermissionObject()->getPageDraftID();
	}

}
