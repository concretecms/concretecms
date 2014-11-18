<?php
namespace Concrete\Core\Permission\Response;
use Page;
use User;
use Group;
use PermissionKey;
use Permissions;
class TopicCategoryTreeNodeResponse extends Response {

	protected function canAccessTopics() {
		$c = Page::getByPath('/dashboard/system/attributes/topics');
		$cp = new Permissions($c);
		return $cp->canViewPage();
	}

	public function canEditTreeNodePermissions() {
		return $this->canAccessTopics();
	}

	public function canViewTreeNode() {
		return $this->validate('view_topic_category_tree_node');
	}

	public function canDuplicateTreeNode() {
		return false;
	}

	public function canEditTreeNode() {
		return $this->canAccessTopics();
	}

	public function canDeleteTreeNode() {
		return $this->canAccessTopics();
	}

	public function canAddTopicTreeNode() {
		return $this->canAccessTopics();
	}

	public function canAddTopicCategoryTreeNode() {
		return $this->canAccessTopics();
	}

	public function canAddTreeSubNode() {
		return $this->canAccessTopics();
	}



}
