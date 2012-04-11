<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('permission/keys/access_user_search');

class AssignUserGroupsUserPermissionKey extends AccessUserSearchUserPermissionKey  {
	
	protected $dbTableAssignments = 'UserPermissionAssignGroupAssignments';
	protected $dbTableAssignmentsCustom = 'UserPermissionAssignGroupAssignmentsCustom';

	public function validate($obj = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
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

class AssignUserGroupsUserPermissionAssignment extends AccessUserSearchUserPermissionAssignment {
	
}