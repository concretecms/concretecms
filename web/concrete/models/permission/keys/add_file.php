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

	public function getAllowedFileExtensions() {
		$u = new User();
		$extensions = array();
		if ($u->isSuperUser()) {
			$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();
			return $extensions;
		}
	
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAccessListItems(FileSetPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		foreach($list as $l) {
			if ($l->getFileTypesAllowedPermission() == 'N') {
				$extensions = array();
			}
			if ($l->getFileTypesAllowedPermission() == 'C') {
				$extensions = array_unique(array_merge($extensions, $l->getFileTypesAllowedArray()));
			}
			if ($l->getFileTypesAllowedPermission() == 'A') {
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
	

}

class AddFileFileSetPermissionAccess extends FileSetPermissionAccess {

	public function getAccessListItems($accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$permission = $db->GetOne('select permission from FileSetPermissionFileTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
			if ($permission != 'N' && $permission != 'C') {
				$permission = 'A';
			}
			$l->setFileTypesAllowedPermission($permission);
			if ($permission == 'C') {
				$extensions = $db->GetCol('select extension from FileSetPermissionFileTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				$l->setFileTypesAllowedArray($extensions);
			}
		}
		return $list;
	}

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from FileSetPermissionFileTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into FileSetPermissionFileTypeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from FileSetPermissionFileTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['extension']);
			$db->Execute('insert into FileSetPermissionFileTypeAccessListCustom  (peID, paID, extension) values (?, ?, ?)', $v);
		}
		return $newPA;
	}
	
	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from FileSetPermissionFileTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from FileSetPermissionFileTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['fileTypesIncluded'])) { 
			foreach($args['fileTypesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into FileSetPermissionFileTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['fileTypesExcluded'])) { 
			foreach($args['fileTypesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into FileSetPermissionFileTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['extensionInclude'])) { 
			foreach($args['extensionInclude'] as $peID => $extensions) {
				foreach($extensions as $extension) { 
					$v = array($this->getPermissionAccessID(), $peID, $extension);
					$db->Execute('insert into FileSetPermissionFileTypeAccessListCustom (paID, peID, extension) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['extensionExclude'])) { 
			foreach($args['extensionExclude'] as $peID => $extensions) {
				foreach($extensions as $extension) { 
					$v = array($this->getPermissionAccessID(), $peID, $extension);
					$db->Execute('insert into FileSetPermissionFileTypeAccessListCustom (paID, peID, extension) values (?, ?, ?)', $v);
				}
			}
		}
	}

}


class AddFileFileSetPermissionAccessListItem extends FileSetPermissionAccessListItem {
	
	protected $customFileTypesArray = array();
	protected $fileTypesAllowedPermission = 'N';

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