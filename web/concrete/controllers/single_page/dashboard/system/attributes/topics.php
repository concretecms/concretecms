<?php 

namespace Concrete\Controller\SinglePage\Dashboard\System\Attributes;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Tree;
use Config;
use Loader;
use Core;
use \Concrete\Core\Tree\Type\Topic as TopicTree;
use \Concrete\Core\Tree\Node\Node as TreeNode;
use \Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use \Concrete\Core\Tree\Node\Type\TopicCategory as TopicCategoryTreeNode;
use Permissions;

class Topics extends DashboardPageController {

	public function view($treeID = false) {
		$defaultTree = TopicTree::getDefault();
		$tree = TopicTree::getByID(Loader::helper('security')->sanitizeInt($treeID));
		if (!$tree) {
			$tree = $defaultTree;
		}

		$this->set('tree', $tree);
		$this->requireAsset('core/topics');

		$trees = array();
		if (is_object($defaultTree)) {
			$trees[] = $defaultTree;
			foreach(TopicTree::getList() as $ctree) {
				if ($ctree->getTreeID() != $defaultTree->getTreeID()) {
					$trees[] = $ctree;
				}
			}
		}
		$this->set('trees', $trees);
	}

	public function tree_added($treeID) {
		$this->set('success', t('Tree added successfully.'));
		$this->view($treeID);
	}


	public function add_category_node($treeNodeParentID) {
		if ($this->token->validate('add_category_node')) {
			$parent = TreeNode::getByID($treeNodeParentID);
			$tree = $parent->getTreeObject();
			$title = $_POST['treeNodeCategoryName'];
			if (!$title) {
				$this->error->add(t('Invalid title for category'));
			}

			if (!is_object($parent)) {
				$this->error->add(t('Invalid parent category'));
			}

			$np = new Permissions($parent);
			if (!$np->canAddTopicCategoryTreeNode()) {
				$this->error->add(t('You may not add a category here.'));
			}

			if (!$this->error->has()) {
				$category = TopicCategoryTreeNode::add($title, $parent);
				$r = $category->getTreeNodeJSON();
				Loader::helper('ajax')->sendResult($r);
			}

		} else {
			$this->error->add($this->token->getErrorMessage());
		}

		if ($this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		}
	}

	public function update_topic_node() {
		if ($this->token->validate('update_topic_node')) {
			$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_POST['treeNodeID']));
			if (!($node instanceof TopicTreeNode)) {
				$this->error->add(t('Invalid node.'));
			}
			$title = $_POST['treeNodeTopicName'];
			if (!$title) {
				$this->error->add(t('Invalid title for topic'));
			}

			$np = new Permissions($node);
			if (!$np->canEditTreeNode()) {
				$this->error->add(t('You may not edit this node.'));
			}

			if (!$this->error->has()) {
				$node->setTreeNodeTopicName($title);
				$r = $node->getTreeNodeJSON();
				Loader::helper('ajax')->sendResult($r);
			}

		} else {
			$this->error->add($this->token->getErrorMessage());
		}

		if ($this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		}
	}
	public function update_category_node() {
		if ($this->token->validate('update_category_node')) {
			$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_POST['treeNodeID']));
			if (!($node instanceof TopicCategoryTreeNode)) {
				$this->error->add(t('Invalid node.'));
			}
			$title = $_POST['treeNodeCategoryName'];
			if (!$title) {
				$this->error->add(t('Invalid title for category'));
			}

			$np = new Permissions($node);
			if (!$np->canEditTreeNode()) {
				$this->error->add(t('You may not edit this node.'));
			}

			if (!$this->error->has()) {
				$node->setTreeNodeCategoryName($title);
				$r = $node->getTreeNodeJSON();
				Loader::helper('ajax')->sendResult($r);
			}

		} else {
			$this->error->add($this->token->getErrorMessage());
		}

		if ($this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		}
	}

	public function add_topic_node($treeNodeParentID) {
		if ($this->token->validate('add_topic_node')) {
			$parent = TreeNode::getByID($treeNodeParentID);
			$tree = $parent->getTreeObject();
			$title = $_POST['treeNodeTopicName'];
			if (!$title) {
				$this->error->add(t('Invalid title for topic'));
			}

			if (!is_object($parent)) {
				$this->error->add(t('Invalid parent category'));
			}

			$np = new Permissions($parent);
			if (!$np->canAddTopicTreeNode()) {
				$this->error->add(t('You may not add a topic here.'));
			}

			if (!$this->error->has()) {
				$category = TopicTreeNode::add($title, $parent);
				$r = $category->getTreeNodeJSON();
				Loader::helper('ajax')->sendResult($r);
			}

		} else {
			$this->error->add($this->token->getErrorMessage());
		}

		if ($this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		}
	}



	public function tree_deleted() {
		$this->set('message', t('Tree deleted successfully.'));
		$this->view();
	}


	public function remove_tree() {
		if ($this->token->validate('remove_tree')) {
			$tree = Tree::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeID']));
			$treeType = $tree->getTreeTypeObject();
			if (is_object($treeType)) {
				$treeTypeHandle = $treeType->getTreeTypeHandle();
			}
			if (is_object($tree) && $treeTypeHandle == 'topic') {
				if (\PermissionKey::getByHandle('remove_topic_tree')->validate()) {
					$tree->delete();
					$this->redirect('/dashboard/system/attributes/topics', 'tree_deleted');
				}
			}	
		}
	}

	public function remove_tree_node() {
		if ($this->token->validate('remove_tree_node')) {
			$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_POST['treeNodeID']));
			$tree = $node->getTreeObject();
			$treeNodeID = $node->getTreeNodeID();
			if (!is_object($node)) {
				$this->error->add(t('Invalid node.'));
			}

			if ($node->getTreeNodeParentID() == 0) {
				$this->error->add(t('You may not remove the top level node.'));
			}

			$np = new Permissions($node);
			if (!$np->canDeleteTreeNode()) {
				$this->error->add(t('You may not remove this node.'));
			}
			
			if ($tree->getTreeTypeHandle() != 'topic') {
				$this->error->add(t('Invalid tree type.'));
			}

			if (!$this->error->has()) {
				$node->delete();
				$r = new \stdClass;
				$r->treeNodeID = $treeNodeID;
				Loader::helper('ajax')->sendResult($r);
			}

		} else {
			$this->error->add($this->token->getErrorMessage());
		}

		if ($this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		}
	}

}