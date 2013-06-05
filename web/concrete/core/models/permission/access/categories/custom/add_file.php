<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AddFileFileSetPermissionAccess extends FileSetPermissionAccess {

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