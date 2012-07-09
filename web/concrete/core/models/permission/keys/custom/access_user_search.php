<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AccessUserSearchUserPermissionKey extends UserPermissionKey  {

	protected function getAllowedGroupIDs($list = false) {

		if (!$list) { 
			$u = new User();
			$accessEntities = $u->getUserAccessEntityObjects();
			$list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
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
		$asl = new AccessUserSearchUserPermissionAccessListItem();
		if ($u->isSuperUser()) {
			$asl->setGroupsAllowedPermission('A');
			return $asl;
		}

		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return $asl;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
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

}