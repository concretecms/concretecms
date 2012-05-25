<?
defined('C5_EXECUTE') or die("Access Denied.");

class ViewUserAttributesUserPermissionKey extends UserPermissionKey  {

	protected function getAllowedAttributeKeyIDs($list = false) {
		if (!$list) {
			$u = new User();
			$accessEntities = $u->getUserAccessEntityObjects();
			$list = $this->getAssignmentList(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
			$list = PermissionDuration::filterByActive($list);
		}
		
		$db = Loader::db();
		$allakIDs = $db->GetCol('select akID from UserAttributeKeys');
		$akIDs = array();
		foreach($list as $l) {
			if ($l->getAttributesAllowedPermission() == 'N') {
				$akIDs = array();
			}
			if ($l->getAttributesAllowedPermission() == 'C') {
				if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
					$akIDs = array_values(array_diff($akIDs, $l->getAttributesAllowedArray()));
				} else { 
					$akIDs = array_unique(array_merge($akIDs, $l->getAttributesAllowedArray()));
				}
			}
			if ($l->getAttributesAllowedPermission() == 'A') {
				$akIDs = $allakIDs;
			}
		}
		
		return $akIDs;
	}
	
	
	public function getMyAssignment() {
		$u = new User();
		$asl = new ViewUserAttributesUserPermissionAssignment();
		if ($u->isSuperUser()) {
			$asl->setAttributesAllowedPermission('A');
			return $asl;
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		foreach($list as $l) {
			if ($l->getAttributesAllowedPermission() == 'N') {
				$asl->setAttributesAllowedPermission('N');
			}

			if ($l->getAttributesAllowedPermission() == 'C') {
				$asl->setAttributesAllowedPermission('C');
			}

			if ($l->getAttributesAllowedPermission() == 'A') {
				$asl->setAttributesAllowedPermission('A');
			}
		}	
		
		$asl->setAttributesAllowedArray($this->getAllowedAttributeKeyIDs($list));
		return $asl;
	}
	
	public function validate($obj = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedAttributeKeyIDs();
		if ($obj != false) {
			if (is_object($obj)) {
				$akID = $obj->getAttributeKeyID();
			} else {
				$akID = $obj;
			}
			return in_array($akID, $types);
		} else {
			return count($types) > 0;
		}
	}	

	
}

class ViewUserAttributesUserPermissionAccess extends UserPermissionAccess {

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from UserPermissionViewAttributeAssignments');
		$db->Execute('delete from UserPermissionViewAttributeAssignmentsCustom');
		if (is_array($args['viewAttributesIncluded'])) { 
			foreach($args['viewAttributesIncluded'] as $peID => $permission) {
				$v = array($peID, $permission);
				$db->Execute('insert into UserPermissionViewAttributeAssignments (peID, permission) values (?, ?)', $v);
			}
		}
		
		if (is_array($args['viewAttributesExcluded'])) { 
			foreach($args['viewAttributesExcluded'] as $peID => $permission) {
				$v = array($peID, $permission);
				$db->Execute('insert into UserPermissionViewAttributeAssignments (peID, permission) values (?, ?)', $v);
			}
		}

		if (is_array($args['akIDInclude'])) { 
			foreach($args['akIDInclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) { 
					$v = array($peID, $akID);
					$db->Execute('insert into UserPermissionViewAttributeAssignmentsCustom (peID, akID) values (?, ?)', $v);
				}
			}
		}

		if (is_array($args['akIDExclude'])) { 
			foreach($args['akIDExclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) { 
					$v = array($peID, $akID);
					$db->Execute('insert into UserPermissionViewAttributeAssignmentsCustom (peID, akID) values (?, ?)', $v);
				}
			}
		}
	}


	public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page && $l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 'A';
			} else { 
				$permission = $db->GetOne('select permission from UserPermissionViewAttributeAssignments where peID = ?', array($pe->getAccessEntityID()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 'A';
				}

			}
			$l->setAttributesAllowedPermission($permission);
			if ($permission == 'C') { 
				$akIDs = $db->GetCol('select akID from UserPermissionViewAttributeAssignmentsCustom where peID = ?', array($pe->getAccessEntityID()));
				$l->setAttributesAllowedArray($akIDs);
			}
		}
		return $list;
	}

}

class ViewUserAttributesUserPermissionAccessListItem extends UserPermissionAccessListItem {
	
	protected $customAttributeArray = array();
	protected $attributesAllowedPermission = 'N';

	public function setAttributesAllowedPermission($permission) {
		$this->attributesAllowedPermission = $permission;
	}
	public function getAttributesAllowedPermission() {
		return $this->attributesAllowedPermission;
	}
	public function setAttributesAllowedArray($akIDs) {
		$this->customAttributeArray = $akIDs;
	}
	public function getAttributesAllowedArray() {
		return $this->customAttributeArray;
	}
	
	
}