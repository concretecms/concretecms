<?php
namespace Concrete\Core\Permission\Response;
use Page;
use User;
use Group;
use PermissionKey;
use Permissions;
class GroupTreeNodeResponse extends TreeNodeResponse {

	public function canEditTreeNodePermissions() {
		return $this->validate('edit_group_permissions');
	}

	public function canViewTreeNode() {
		$c = Page::getByPath('/dashboard/users/groups');
		$cp = new Permissions($c);
		return $cp->canViewPage();
	}

	public function canDuplicateTreeNode() {
		return false;
	}

	public function canEditTreeNode() {
		return $this->validate('edit_group');
	}

	public function canAddTreeSubNode() {
		return $this->validate('add_sub_group');
	}

	public function canDeleteTreeNode() {
		return false;
	}

}
