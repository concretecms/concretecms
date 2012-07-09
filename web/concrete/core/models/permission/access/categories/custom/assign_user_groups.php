<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AssignUserGroupsUserPermissionAccess extends AccessUserSearchUserPermissionAccess {

	protected $dbTableAccessList = 'UserPermissionAssignGroupAccessList';
	protected $dbTableAccessListCustom = 'UserPermissionAssignGroupAccessListCustom';
	
}