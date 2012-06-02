<?
defined('C5_EXECUTE') or die("Access Denied.");
class FilePermissionKey extends PermissionKey {


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
				$valid = ($this->getPermissionObject()->getUserID() == $u->getUserID());
			}
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
				$valid = false;
			}
		}
		return $valid;		
	}
	
	
	public function getPermissionAccessObject() {
		$permID = $this->getPermissionAccessID();
		$perms = array();
		if (is_array($permID)) {
			foreach($permID as $paID) {
				$pa = PermissionAccess::getByID($paID, $this);
				if (is_object($pa)) {
					$perms[] = $pa;
				}
			}
			return PermissionAccess::createByMerge($perms);
		} else {
			return parent::getPermissionAccessObject();
		}
	}
	

	public function copyFromFileSetToFile() {
		$db = Loader::db();
		$paID = $this->getPermissionAccessID();
		if (is_array($paID)) {
			// we have to merge the permissions access object into a new one.
			$pa = PermissionAccess::create($this);
			foreach($paID as $paID) {
				$pax = PermissionAccess::getByID($paID, $this);
				$pax->duplicate($pa);
			}
			$paID = $pa->getPermissionAccessID();
		}
		if ($paID) {
			$db = Loader::db();
			$db->Replace('FilePermissionAssignments', array(
				'fID' => $this->permissionObject->getFileID(), 
				'pkID' => $this->getPermissionKeyID(),
				'paID' => $paID), array('fID', 'paID', 'pkID'), true);				

		}
	}



}

class FilePermissionTarget extends PermissionTarget {

	protected $permissionObjectToCheck;
	
	protected $inheritedPermissions = array(
		'view_file' => 'view_file_set_file',
		'view_file_in_file_manager' => 'search_file_set',
		'edit_file_properties' => 'edit_file_set_file_properties',
		'edit_file_contents' => 'edit_file_set_file_contents',
		'copy_file' => 'copy_file_set_files',
		'edit_file_permissions' => 'edit_file_set_permissions',
		'delete_file' => 'delete_file_set_files'
	);
	
	public function getPermissionAccessID() {
		$db = Loader::db();
		if ($this->permissionObjectToCheck instanceof File) { 
 			$r = $db->GetCol('select paID from FilePermissionAssignments where fID = ? and pkID = ?', array(
 			$this->permissionObject->getFileID(), $this->pk->getPermissionKeyID()
 			));
 		} else if (is_array($this->permissionObjectToCheck)) { // sets
			$sets = array();
			foreach($this->permissionObjectToCheck as $fs) {
				$sets[] = $fs->getFileSetID();
			}
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]));
			$r = $db->GetCol('select distinct paID from FileSetPermissionAssignments where fsID in (' . implode(',', $sets) . ') and pkID = ? ' . $filterString, array(
				$inheritedPKID
			));
		} else if ($this->permissionObjectToCheck instanceof FileSet && isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]));
			$r = $db->GetCol('select distinct paID from FileSetPermissionAssignments where fsID = ? and pkID = ?', array(
				$this->permissionObjectToCheck->getFileSetID(), $inheritedPKID
			));
		} else {
			return false;
		}
		
		if (count($r) == 1) {
			return $r[0];
		}
		if (count($r) > 1) {
			return $r;
		}

	}

	public function setPermissionObject(File $f) {
		$this->permissionObject = $f;
		
		if ($f->overrideFileSetPermissions()) {
			$this->permissionObjectToCheck = $f;
		} else {
			$sets = $f->getFileSets();
			$permsets = array();
			foreach($sets as $fs) {
				if ($fs->overrideGlobalPermissions()) {
					$permsets[] = $fs;
				}
			}
			if (count($permsets) > 0) {
				$this->permissionObjectToCheck = $permsets;
			} else { 
				$fs = FileSet::getGlobal();
				$this->permissionObjectToCheck = $fs;
			}
		}
	}


	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update FilePermissionAssignments set paID = 0 where pkID = ? and fID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getFileID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('FilePermissionAssignments', array('fID' => $this->getPermissionObject()->getFileID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('fID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&fID=' . $this->getPermissionObject()->getFileID();
	}

}

class FilePermissionAccessListItem extends PermissionAccessListItem {
	
}

class FilePermissionAccess extends PermissionAccess {


}