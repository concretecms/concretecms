<?php
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Tree\Node\Node as TreeNode;
$sourceNode = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['sourceTreeNodeID']));
$destNode = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
if (is_object($sourceNode) && is_object($destNode)) {
	$sp = new Permissions($sourceNode);
	$dp = new Permissions($destNode);
	if ($dp->canAddTreeSubNode()) {
		$sourceNode->move($destNode);
		$destNode->saveChildOrder($_POST['treeNodeID']);
		print Loader::helper('ajax')->sendResult($destNode->getTreeNodeJSON());
	} else {
		$r = new stdClass;
		$r->error = true;
		$r->message = t('You do not have permission to drag this node here.');
		print Loader::helper('ajax')->sendResult($r);
	}
}