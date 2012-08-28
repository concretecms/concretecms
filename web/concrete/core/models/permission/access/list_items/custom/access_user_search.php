<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AccessUserSearchUserPermissionAccessListItem extends PermissionAccessListItem {
	
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