<?
namespace Concrete\Core\Foundation\Tree\Node\Type;
use Concrete\Core\Foundation\Tree\Node\Node;
class TopicCategory extends Category {

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