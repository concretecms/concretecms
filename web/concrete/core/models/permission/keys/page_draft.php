<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageDraftPermissionKey extends PermissionKey {
	
	public function copyFromPageTypeToPageDraft() {
		$paID = $this->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$pDraftID = $this->permissionObject->getPageDraftID();
			$db->Replace('PageDraftPermissionAssignments', array(
				'pDraftID' => $pDraftID,
				'pkID' => $this->getPermissionKeyID(),
				'paID' => $paID), array('pDraftID', 'paID', 'pkID'), true);				
		}
	}


}