<?php
namespace Concrete\Core\Tree\Node\Type;
use Permissions;
class TopicCategory extends Category {

	public function getPermissionResponseClassName() {
		return '\\Concrete\\Core\\Permission\\Response\\TopicCategoryTreeNodeResponse';
	}

	public function getPermissionAssignmentClassName() {
		return '\\Concrete\\Core\\Permission\\Assignment\\TopicCategoryTreeNodeAssignment';
	}
	public function getPermissionObjectKeyCategoryHandle() {
		return 'topic_category_tree_node';
	}

	public function getTreeNodeJSON() {
		$obj = parent::getTreeNodeJSON();
		if (is_object($obj)) {
			$p = new Permissions($this);
			$obj->canAddTopicTreeNode = $p->canAddTopicTreeNode();
			$obj->canAddTopicCategoryTreeNode = $p->canAddTopicCategoryTreeNode();
			return $obj;
		}
	}




}
