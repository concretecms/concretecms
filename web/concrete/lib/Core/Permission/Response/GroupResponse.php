<?
namespace Concrete\Core\Permission\Response;
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