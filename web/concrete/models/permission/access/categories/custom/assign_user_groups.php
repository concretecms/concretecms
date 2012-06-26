<?
defined('C5_EXECUTE') or die("Access Denied.");
class AssignUserGroupsUserPermissionAccess extends AccessUserSearchUserPermissionAccess {

	protected $dbTableAccessList = 'UserPermissionAssignGroupAccessList';
	protected $dbTableAccessListCustom = 'UserPermissionAssignGroupAccessListCustom';
	
}