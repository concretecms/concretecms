<?
class Concrete5_Model_TopicCategoryTreeNode extends CategoryTreeNode {

	public function getTreeNodePermissionKeyCategoryHandle() { return 'topic_category_tree_node';}
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