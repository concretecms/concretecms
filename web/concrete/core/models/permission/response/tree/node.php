<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_TreeNodePermissionResponse extends PermissionResponse {

	abstract public function canViewTreeNode();
	abstract public function canDeleteTreeNode();
	abstract public function canEditTreeNodePermissions();
	abstract public function canEditTreeNode();
	abstract public function canAddTreeSubNode();

}