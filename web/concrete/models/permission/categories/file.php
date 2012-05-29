<?
defined('C5_EXECUTE') or die("Access Denied.");
class FilePermissionKey extends PermissionKey {

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
	
	public function getPermissionAccessID() {
		$db = Loader::db();
		if ($this->permissionObjectToCheck instanceof File) { 
 			$r = $db->GetCol('select paID from FilePermissionAssignments where fID = ? and pkID = ?', array(
 			$this->permissionObject->getFileID(), $this->getPermissionKeyID()
 			));
 		} else if (is_array($this->permissionObjectToCheck)) { // sets
			$sets = array();
			foreach($this->permissionObjectToCheck as $fs) {
				$sets[] = $fs->getFileSetID();
			}
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->GetCol('select distinct paID from FileSetPermissionAssignments where fsID in (' . implode(',', $sets) . ') and pkID = ? ' . $filterString, array(
				$inheritedPKID
			));
			Database::setDebug(false);
		} else if ($this->permissionObjectToCheck instanceof FileSet && isset($this->inheritedPermissions[$this->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
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
	
	
	/*	
	public function getAssignmentList($accessType = FilePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
		if ($this->permissionObjectToCheck instanceof File) { 
 			$r = $db->Execute('select peID, pdID, accessType from FilePermissionAssignments where fID = ? and pkID = ? ' . $filterString, array(
 			$this->permissionObject->getFileID(), $this->getPermissionKeyID()
 			));
 		} else if (is_array($this->permissionObjectToCheck)) { // sets
			$sets = array();
			foreach($this->permissionObjectToCheck as $fs) {
				$sets[] = $fs->getFileSetID();
			}
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select distinct peID, accessType, pdID from FileSetPermissionAssignments where fsID in (' . implode(',', $sets) . ') and pkID = ? ' . $filterString, array(
				$inheritedPKID
			));
		} else if ($this->permissionObjectToCheck instanceof FileSet && isset($this->inheritedPermissions[$this->getPermissionKeyHandle()])) { 
			$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->getPermissionKeyHandle()]));
			$r = $db->Execute('select accessType, peID, pdID from FileSetPermissionAssignments where fsID = ? and pkID = ? ' . $filterString, array(
				$this->permissionObjectToCheck->getFileSetID(), $inheritedPKID
			));
		} else {
			return array();
		}
 		
 		$list = array();
 		$class = str_replace('FilePermissionKey', 'FilePermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'FilePermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($row['accessType']);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPermissionObject($this->permissionObject);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = FilePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('FilePermissionAssignments', array(
			'fID' => $this->permissionObject->getFileID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('fID', 'peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from FilePermissionAssignments where fID = ? and peID = ? and pkID = ?', array($this->permissionObject->getFileID(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	*/
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&fID=' . $this->getPermissionObject()->getFileID();
	}

	public function clearWorkflows() {
		$db = Loader::db();
		$db->Execute('delete from FilePermissionWorkflows where fID = ? and pkID = ?', array($this->getPermissionObject()->getFileID(), $this->getPermissionKeyID()));
	}
	
	public function attachWorkflow(Workflow $wf) {
		$db = Loader::db();
		$db->Replace('FilePermissionWorkflows', array('fID' => $this->getPermissionObject()->getFileID(), 'pkID' => $this->getPermissionKeyID(), 'wfID' => $wf->getWorkflowID()), array('fID', 'pkID', 'wfID'), true);
	}

	public function getWorkflows() {
		$db = Loader::db();
		$r = $db->Execute('select wfID from PagePermissionWorkflows where fID = ? and pkID = ?', array($this->getPermissionObject()->getFileID(), $this->getPermissionKeyID()));
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

class FilePermissionAccessListItem extends PermissionAccessListItem {
	
}

class FilePermissionAccess extends PermissionAccess {


}