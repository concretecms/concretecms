<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_GroupTreeNodePermissionResponse extends TreeNodePermissionResponse {

	protected function canAccessGroups() {
		$c = Page::getByPath('/dashboard/users/nested_groups');
		$cp = new Permissions($c);
		return $cp->canViewPage();
	}

	public function canEditTreeNodePermissions() {
		return $this->canAccessGroups();
	}

	public function canViewTreeNode() {
		return $this->validate('view_group_tree_node');
	}

	public function canDuplicateTreeNode() {
		return $this->canAccessGroups();
	}

	public function canEditTreeNode() {
		return $this->canAccessGroups();
	}

	public function canDeleteTreeNode() {
		return $this->canAccessGroups();
	}

}