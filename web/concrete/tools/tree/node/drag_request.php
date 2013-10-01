<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$sourceNode = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['sourceTreeNodeID']));
$destNode = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
if (is_object($sourceNode) && is_object($destNode)) {
	$sp = new Permissions($sourceNode);
	$dp = new Permissions($destNode);
	if ($sp->canEditTreeNode() && $dp->canEditTreeNode()) {
		$sourceNode->move($destNode);
		$destNode->saveChildOrder($_POST['treeNodeID']);
		print Loader::helper('ajax')->sendResult($destNode->getTreeNodeJSON());
	}
}