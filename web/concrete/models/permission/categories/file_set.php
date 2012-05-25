<?
defined('C5_EXECUTE') or die("Access Denied.");
class FileSetPermissionKey extends PermissionKey {

	const ACCESS_TYPE_MINE = 5;
	protected $permissionObjectToCheck;
	
	public function getSupportedAccessTypes() {
		$types = array(
			self::ACCESS_TYPE_INCLUDE => t('Included'),
			self::ACCESS_TYPE_MINE => t('Mine'),
			self::ACCESS_TYPE_EXCLUDE => t('Excluded'),
		);
		return $types;
	}

	public function setPermissionObject(FileSet $fs) {
		$this->permissionObject = $fs;
		
		if ($fs->overrideGlobalPermissions()) {
			$this->permissionObjectToCheck = $fs;
		} else {
			$fs = FileSet::getGlobal();
			$this->permissionObjectToCheck = $fs;
		}
	}

	public function validate() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$accessEntities = $u->getUserAccessEntityObjects();
		$valid = false;
		$list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$valid = true;
			}
			if ($l->getAccessType() == FileSetPermissionKey::ACCESS_TYPE_MINE) {
				$valid = true;
			}
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
				$valid = false;
			}
		}
		return $valid;		
	}
	
	public function getPermissionAccessID() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from FileSetPermissionAssignments where fsID = ? and pkID = ?', array(
 			$this->permissionObjectToCheck->getFileSetID(), $this->getPermissionKeyID()
 		));
 		return $r;
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update FileSetPermissionAssignments set paID = 0 where pkID = ? and fsID = ?', array($this->pkID, $this->permissionObject->getFileSetID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('FileSetPermissionAssignments', array('fsID' => $this->getPermissionObject()->getFileSetID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pkID), array('fsID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&fsID=' . $this->getPermissionObject()->getFileSetID();
	}

	public function clearWorkflows() {
		$db = Loader::db();
		$db->Execute('delete from FileSetPermissionWorkflows where fsID = ? and pkID = ?', array($this->getPermissionObject()->getFileSetID(), $this->getPermissionKeyID()));
	}
	
	public function attachWorkflow(Workflow $wf) {
		$db = Loader::db();
		$db->Replace('FileSetPermissionWorkflows', array('fsID' => $this->getPermissionObject()->getFileSetID(), 'pkID' => $this->getPermissionKeyID(), 'wfID' => $wf->getWorkflowID()), array('fsID', 'pkID', 'wfID'), true);
	}

	public function getWorkflows() {
		$db = Loader::db();
		$r = $db->Execute('select wfID from FileSetPermissionWorkflows where fsID = ? and pkID = ?', array($this->getPermissionObject()->getFileSetID(), $this->getPermissionKeyID()));
		$workflows = array();
		while ($row = $r->FetchRow()) {
			$wf = Workflow::getByID($row['wfID']);
			if (is_object($wf)) {
				$workflows[] = $wf;
			}
		}
		return $workflows;
	}

}

class FileSetPermissionAccess extends PermissionAccess {


}

class FileSetPermissionAccessListItem extends PermissionAccessListItem {


}

/**
 * legacy
 */
class FilePermissions {

	public static function getGlobal() {
		$fs = FileSet::getGlobal();
		$fsp = new Permissions($fs);
		return $fsp;
	}
}