<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class TreeNodePermissionResponse extends PermissionResponse {

	abstract public function canViewTreeNode();
	abstract public function canDeleteTreeNode();
	abstract public function canEditTreeNodePermissions();
	abstract public function canEditTreeNode();

}