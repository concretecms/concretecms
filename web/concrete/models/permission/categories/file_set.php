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
	


}

class FileSetPermissionAssignment extends PermissionAssignment {
	
	public function setPermissionObject(FileSet $fs) {
		$this->permissionObject = $fs;
		
		if ($fs->overrideGlobalPermissions()) {
			$this->permissionObjectToCheck = $fs;
		} else {
			$fs = FileSet::getGlobal();
			$this->permissionObjectToCheck = $fs;
		}
	}

	public function getPermissionAccessObject() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from FileSetPermissionAssignments where fsID = ? and pkID = ?', array(
 			$this->permissionObjectToCheck->getFileSetID(), $this->pk->getPermissionKeyID()
 		));
 		return PermissionAccess::getByID($r, $this->pk);
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update FileSetPermissionAssignments set paID = 0 where pkID = ? and fsID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getFileSetID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('FileSetPermissionAssignments', array('fsID' => $this->getPermissionObject()->getFileSetID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('fsID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&fsID=' . $this->getPermissionObject()->getFileSetID();
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