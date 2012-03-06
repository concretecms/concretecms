<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddBlockAreaPermissionKey extends AreaPermissionKey  {

	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from AreaPermissionBlockTypeAssignments where cID = ? and arHandle = ?', array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle()));
		$db->Execute('delete from AreaPermissionBlockTypeAssignmentsCustom where cID = ? and arHandle = ?', array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle()));
		if (is_array($args['blockTypesIncluded'])) { 
			foreach($args['blockTypesIncluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAssignments (cID, arHandle, peID, permission) values (?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['blockTypesExcluded'])) { 
			foreach($args['blockTypesExcluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAssignments (cID, arHandle, peID, permission) values (?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['btIDInclude'])) { 
			foreach($args['btIDInclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAssignmentsCustom (cID, arHandle, peID, btID) values (?, ?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['btIDExclude'])) { 
			foreach($args['btIDExclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAssignmentsCustom (cID, arHandle, peID, btID) values (?, ?, ?, ?)', $v);
				}
			}
		}

	}
	
	public function validate($bt = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(AreaPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		// these are assignments that apply to me
		$canAddBlockType = false;
		foreach($list as $l) {
			if ($l->getBlockTypesAllowedPermission() == 'N') {
				$canAddBlockType = false;
			}
			if ($l->getBlockTypesAllowedPermission() == 'C') {
				if (is_object($bt)) { 
					if ($l->getAccessType() == AreaPermissionKey::ACCESS_TYPE_EXCLUDE) {
						$canAddBlockType = !in_array($bt->getBlockTypeID(), $l->getBlockTypesAllowedArray());
					} else { 
						$canAddBlockType = in_array($bt->getBlockTypeID(), $l->getBlockTypesAllowedArray());
					}
				} else {
					$canAddBlockType = true;
				}
			}
			if ($l->getBlockTypesAllowedPermission() == 'A') {
				$canAddBlockType = true;
			}
		}
		
		return $canAddBlockType;
	}


	public function getAssignmentList($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page && $l->getAccessType() == AreaPermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 1;
			} else { 
				$permission = $db->GetOne('select permission from AreaPermissionBlockTypeAssignments where peID = ? and cID = ? and arHandle = ?', array($pe->getAccessEntityID(), $this->permissionObjectToCheck->getCollectionID(), $this->permissionObjectToCheck->getAreaHandle()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 1;
				}

			}
			$l->setBlockTypesAllowedPermission($permission);
			if ($permission == 'C') { 
				if ($this->permissionObjectToCheck instanceof Area) { 
					$cID = $this->permissionObjectToCheck->getCollectionID();
				} else {
					$cID = $this->permissionObjectToCheck->getPermissionsCollectionID();
				}
				$arHandle = $this->permissionObjectToCheck->getAreaHandle();
				$ctIDs = $db->GetCol('select btID from AreaPermissionBlockTypeAssignmentsCustom where peID = ? and cID = ? and arHandle = ?', array($pe->getAccessEntityID(), $cID, $arHandle));
				$l->setBlockTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
	
}

class AddBlockAreaPermissionAssignment extends AreaPermissionAssignment {
	
	protected $customBlockTypeArray = array();
	protected $blockTypesAllowedPermission = 'N';

	public function setBlockTypesAllowedPermission($permission) {
		$this->blockTypesAllowedPermission = $permission;
	}
	public function getBlockTypesAllowedPermission() {
		return $this->blockTypesAllowedPermission;
	}
	public function setBlockTypesAllowedArray($ctIDs) {
		$this->customBlockTypeArray = $ctIDs;
	}
	public function getBlockTypesAllowedArray() {
		return $this->customBlockTypeArray;
	}
	
	
}