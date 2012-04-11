<?
defined('C5_EXECUTE') or die("Access Denied.");

class AccessUserSearchUserPermissionKey extends UserPermissionKey  {

	protected $dbTableAssignments = 'UserPermissionUserSearchAssignments';
	protected $dbTableAssignmentsCustom = 'UserPermissionUserSearchAssignmentsCustom';

	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from ' . $this->dbTableAssignments);
		$db->Execute('delete from ' . $this->dbTableAssignmentsCustom);
		if (is_array($args['groupsIncluded'])) { 
			foreach($args['groupsIncluded'] as $peID => $permission) {
				$v = array($peID, $permission);
				$db->Execute('insert into ' . $this->dbTableAssignments . ' (peID, permission) values (?, ?)', $v);
			}
		}
		
		if (is_array($args['groupsExcluded'])) { 
			foreach($args['groupsExcluded'] as $peID => $permission) {
				$v = array($peID, $permission);
				$db->Execute('insert into ' . $this->dbTableAssignments . ' (peID, permission) values (?, ?)', $v);
			}
		}

		if (is_array($args['gIDInclude'])) { 
			foreach($args['gIDInclude'] as $peID => $gIDs) {
				foreach($gIDs as $gID) { 
					$v = array($peID, $gID);
					$db->Execute('insert into ' . $this->dbTableAssignmentsCustom . ' (peID, gID) values (?, ?)', $v);
				}
			}
		}

		if (is_array($args['gIDExclude'])) { 
			foreach($args['gIDExclude'] as $peID => $gIDs) {
				foreach($gIDs as $gID) { 
					$v = array($peID, $gID);
					$db->Execute('insert into ' . $this->dbTableAssignmentsCustom . ' (peID, gID) values (?, ?)', $v);
				}
			}
		}
	}

	protected function getAllowedGroupIDs($list = false) {

		if (!$list) { 
			$u = new User();
			$accessEntities = $u->getUserAccessEntityObjects();
			$list = $this->getAssignmentList(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
			$list = PermissionDuration::filterByActive($list);
		}
		
		$db = Loader::db();
		$dsh = Loader::helper('concrete/dashboard');
		$allgIDs = $db->GetCol('select gID from Groups');
		$gIDs = array();
		foreach($list as $l) {
			if ($l->getGroupsAllowedPermission() == 'N') {
				$gIDs = array();
			}
			if ($l->getGroupsAllowedPermission() == 'C') {
				if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
					$gIDs = array_values(array_diff($gIDs, $l->getGroupsAllowedArray()));
				} else { 
					$gIDs = array_unique(array_merge($gIDs, $l->getGroupsAllowedArray()));
				}
			}
			if ($l->getGroupsAllowedPermission() == 'A') {
				$gIDs = $allgIDs;
			}
		}
		
		return $gIDs;
	}
	
	
	public function getMyAssignment() {
		$u = new User();
		$asl = new AccessUserSearchUserPermissionAssignment();
		if ($u->isSuperUser()) {
			$asl->setGroupsAllowedPermission('A');
			return $asl;
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		
		$u = new User();
		foreach($list as $l) {
			if ($l->getGroupsAllowedPermission() == 'N') {
				$asl->setGroupsAllowedPermission('N');
			}

			if ($l->getGroupsAllowedPermission() == 'C') {
				$asl->setGroupsAllowedPermission('C');
			}

			if ($l->getGroupsAllowedPermission() == 'A') {
				$asl->setGroupsAllowedPermission('A');
			}
		}	
		if ($asl->getGroupsAllowedPermission() == 'C') { 
			$asl->setGroupsAllowedArray($this->getAllowedGroupIDs());
		}
		return $asl;
	}
	
	public function validate($obj = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		if (is_object($obj) && $obj instanceof UserInfo) {
			$db = Loader::db();
			$asl = $this->getMyAssignment();
			if ($asl->getGroupsAllowedPermission() == 'A') {
				return true;
			} else if ($asl->getGroupsAllowedPermission() == 'C') {
				if (in_array(REGISTERED_GROUP_ID, $asl->getGroupsAllowedArray())) {
					$cnt = $db->GetOne('select count(u.uID) from Users u left join UserGroups ug on u.uID = ug.uID where u.uID = ? and u.uID > ' . USER_SUPER_ID . ' and (gID is null or gID in (' . implode(',', $asl->getGroupsAllowedArray()) . '))', array($obj->getUserID()));
				} else {
					$cnt = $db->GetOne('select count(u.uID) from Users u left join UserGroups ug on u.uID = ug.uID where u.uID = ? and u.uID > ' . USER_SUPER_ID . ' and gID in (' . implode(',', $asl->getGroupsAllowedArray()) . ')', array($obj->getUserID()));
				}
				return $cnt > 0;
			} else {
				return false;
			}				
		}
		
		$types = $this->getAllowedGroupIDs();
		if ($obj != false) {
			if (is_object($obj)) {
				$gID = $obj->getGroupID();
			} else {
				$gID = $obj;
			}
			return in_array($gID, $types);
		} else {
			return count($types) > 0;
		}
	}	

	public function getAssignmentList($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page && $l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 'A';
			} else { 
				$permission = $db->GetOne('select permission from ' . $this->dbTableAssignments . ' where peID = ?', array($pe->getAccessEntityID()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 'A';
				}

			}
			$l->setGroupsAllowedPermission($permission);
			if ($permission == 'C') { 
				$gIDs = $db->GetCol('select gID from ' . $this->dbTableAssignmentsCustom . ' where peID = ?', array($pe->getAccessEntityID()));
				$l->setGroupsAllowedArray($gIDs);
			}
		}
		return $list;
	}
	
}

class AccessUserSearchUserPermissionAssignment extends UserPermissionAssignment {
	
	protected $customGroupArray = array();
	protected $groupsAllowedPermission = 'N';

	public function setGroupsAllowedPermission($permission) {
		$this->groupsAllowedPermission = $permission;
	}
	public function getGroupsAllowedPermission() {
		return $this->groupsAllowedPermission;
	}
	public function setGroupsAllowedArray($gIDs) {
		$this->customGroupArray = $gIDs;
	}
	public function getGroupsAllowedArray() {
		return $this->customGroupArray;
	}
	
	
}