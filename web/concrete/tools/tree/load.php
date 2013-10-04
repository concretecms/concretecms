<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$form = Loader::helper('form');
$treeID = Loader::helper('security')->sanitizeInt($_REQUEST['treeID']);
$tree = Tree::getByID($treeID);
if (!is_object($tree)) {
	exit;
}

if ($_REQUEST['treeNodeSelectedID']) {
	// starting multiple node stuff
	$nodeIDs = explode(',', $_REQUEST['treeNodeSelectedID']);
	if(count($nodeIDs) > 0) {
		foreach($nodeIDs as $nID) {
			$node = TreeNode::getByID($nID);
			if (is_object($node) && $node->getTreeID() == $tree->getTreeID()) {
				$tree->setSelectedTreeNodeID($node->getTreeNodeID());
			}
		}	//end multiple node stuff
	}
}

$result = $tree->getJSON();
print Loader::helper('ajax')->sendResult($result);