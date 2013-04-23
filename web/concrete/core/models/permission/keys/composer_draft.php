<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerDraftPermissionKey extends PermissionKey {
	
	public function copyFromComposerToComposerDraft() {
		$paID = $this->getPermissionAccessID();
		if ($paID) { 
			$db = Loader::db();
			$cmpDraftID = $this->permissionObject->getComposerDraftID();
			$db->Replace('ComposerDraftPermissionAssignments', array(
				'cmpDraftID' => $cmpDraftID,
				'pkID' => $this->getPermissionKeyID(),
				'paID' => $paID), array('cmpDraftID', 'paID', 'pkID'), true);				
		}
	}


}