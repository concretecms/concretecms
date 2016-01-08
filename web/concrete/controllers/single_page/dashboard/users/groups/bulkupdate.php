<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users\Groups;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Tree\Type\Group as GroupTree;
use \Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Group;
use Loader;
use GroupList;
use Concrete\Core\Tree\Node\Node as TreeNode;

class Bulkupdate extends DashboardPageController {

	public function confirm() 
	{
		$this->move();

		if (!$this->error->has()) {
			$selectedGroups = $this->get('selectedGroups');
			$gParentNode = $this->get('gParentNode');

			foreach($selectedGroups as $g) {
				$node = GroupTreeNode::getTreeNodeByGroupID($g->getGroupID());
				if (is_object($node)) {
					$node->move($gParentNode);
				}
			}
		}

		$this->redirect('/dashboard/users/groups', 'bulk_update_complete');
	}

	public function move() 
	{
		$this->search();
		$gParentNodeID = Loader::helper('security')->sanitizeInt($_REQUEST['gParentNodeID']);
		if ($gParentNodeID) {
			$node = TreeNode::getByID($gParentNodeID);
		}
		if (!($node instanceof GroupTreeNode)) {
			$this->error->add(t("Invalid target parent group."));
		}
		$selectedGroups = array();
		if (is_array($_POST['gID'])) {
			foreach($_POST['gID'] as $gID) {
				$group = Group::getByID($gID);
				if (is_object($group)) {
					$selectedGroups[] = $group;
				}
			}
		}

		if (count($selectedGroups) == 0) {
			$this->error->add(t("You must select at least one group to move"));
		}

		if (!$this->error->has()) {
			$gParent = $node->getTreeNodeGroupObject();
			$this->set('selectedGroups', $selectedGroups);
			$this->set('gParent', $gParent);
			$this->set('gParentNode', $node);
		}

	}
	
	public function search() 
	{
		$this->requireAsset('core/groups');
		$tree = GroupTree::get();
		$this->set("tree", $tree);
		$gName = Loader::helper('security')->sanitizeString($_REQUEST['gName']);
		if (!$gName) {
			$this->error->add(t('You must specify a search string.'));
		}
		if (!$this->error->has()) {
			$gl = new GroupList();
			$gl->filterByKeywords($gName);
			$this->set('groups', $gl->getResults());
		}
	}

}