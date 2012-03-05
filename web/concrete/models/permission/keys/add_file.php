<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddFileFileSetPermissionKey extends FileSetPermissionKey  {

	public function getSupportedAccessTypes() {
		$types = array(
			self::ACCESS_TYPE_INCLUDE => t('Included'),
			self::ACCESS_TYPE_EXCLUDE => t('Excluded')
		);
		return $types;
	}

	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from FileSetPermissionFileTypeAssignments where fsID = ?', array($this->permissionObject->getFileSetID()));
		$db->Execute('delete from FileSetPermissionFileTypeAssignmentsCustom where fsID = ?', array($this->permissionObject->getFileSetID()));
		if (is_array($args['fileTypesIncluded'])) { 
			foreach($args['fileTypesIncluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getFileSetID(), $peID, $permission);
				$db->Execute('insert into FileSetPermissionFileTypeAssignments (fsID, peID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['fileTypesExcluded'])) { 
			foreach($args['fileTypesExcluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getFileSetID(), $peID, $permission);
				$db->Execute('insert into FileSetPermissionFileTypeAssignments (fsID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['extensionInclude'])) { 
			foreach($args['extensionInclude'] as $peID => $extensions) {
				foreach($extensions as $extension) { 
					$v = array($this->permissionObject->getFileSetID(), $peID, $extension);
					$db->Execute('insert into FileSetPermissionFileTypeAssignmentsCustom (fsID, peID, extension) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['extensionExclude'])) { 
			foreach($args['extensionExclude'] as $peID => $extensions) {
				foreach($extensions as $extension) { 
					$v = array($this->permissionObject->getFileSetID(), $peID, $extension);
					$db->Execute('insert into FileSetPermissionFileTypeAssignmentsCustom (fsID, peID, extension) values (?, ?, ?)', $v);
				}
			}
		}
	}
	
	public function getAllowedFileExtensions() {
		$u = new User();
		$extensions = array();
		if ($u->isSuperUser()) {
			$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();
			return $extensions;
		}
	
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(FileSetPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		foreach($list as $l) {
			if ($l->getFileTypesAllowedPermission() == '0') {
				$extensions = array();
			}
			if ($l->getFileTypesAllowedPermission() == 'C') {
				$extensions = array_unique(array_merge($extensions, $l->getFileTypesAllowedArray()));
			}
			if ($l->getFileTypesAllowedPermission() == '1') {
				$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();
			}
		}
		
		return $extensions;
	}
	
	public function validate($extension = false) {
		$extensions = $this->getAllowedFileExtensions();
		if ($ext != false) {
			return in_array($extension, $extensions);
		} else {
			return count($extensions) > 0;
		}
	}
	

	public function getAssignmentList($accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$permission = $db->GetOne('select permission from FileSetPermissionFileTypeAssignments where peID = ? and fsID = ?', array($pe->getAccessEntityID(), $this->permissionObject->getFileSetID()));
			if ($permission !== '0' && $permission != 'C') {
				$permission = 1;
			}
			$l->setFileTypesAllowedPermission($permission);
			if ($permission == 'C') { 
				$extensions = $db->GetCol('select extension from FileSetPermissionFileTypeAssignmentsCustom where peID = ? and fsID = ?', array($pe->getAccessEntityID(), $this->permissionObject->getFileSetID()));
				$l->setFileTypesAllowedArray($extensions);
			}
		}
		return $list;
	}

}

class AddFileFileSetPermissionAssignment extends FileSetPermissionAssignment {
	
	protected $customFileTypesArray = array();
	protected $fileTypesAllowedPermission = 0;

	public function setFileTypesAllowedPermission($permission) {
		$this->fileTypesAllowedPermission = $permission;
	}
	public function getFileTypesAllowedPermission() {
		return $this->fileTypesAllowedPermission;
	}
	public function setFileTypesAllowedArray($extensions) {
		$this->customFileTypesArray = $extensions;
	}
	public function getFileTypesAllowedArray() {
		return $this->customFileTypesArray;
	}
	
	
}