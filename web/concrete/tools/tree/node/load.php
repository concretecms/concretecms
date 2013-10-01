<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
$selectedNodeIDs = Loader::helper('security')->sanitizeString($_REQUEST['treeNodeSelectedID']);
$allChildren = $_REQUEST['allChildren'];
if (is_object($node)) {
	$np = new Permissions($node);
	if ($np->canViewTreeNode()) {
		if($allChildren) {
			$node->populateChildren();
		} else {
			$node->populateDirectChildrenOnly();
		}
		$r = array();
		if($selectedNodeIDs) {
			$selectedIDs = explode(',', $selectedNodeIDs);
			foreach($selectedIDs as $match) {
				$node->selectChildrenNodesByID($match);
			}
		}
		foreach($node->getChildNodes() as $childnode) {
			$json = $childnode->getTreeNodeJSON();
			if ($json) {
				$r[] = $json;
			}
		}
		print Loader::helper('ajax')->sendResult($r);
	}
}