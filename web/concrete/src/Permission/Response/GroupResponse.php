<?php
namespace Concrete\Core\Permission\Response;
use Page;
use User;
use Group;
use \Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use PermissionKey;
use Permissions;
class GroupResponse extends Response {

	protected function getTreeGroupNodePermissions() {
		$group = $this->getPermissionObject();
		$node = GroupTreeNode::getTreeNodeByGroupID($group->getGroupID());
		return new Permissions($node);
	}

	public function __call($nm, $arguments) {
		$p = $this->getTreeGroupNodePermissions();
		return call_user_func_array(array($p, $nm), $arguments);
	}




}
