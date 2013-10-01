<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$form = Loader::helper('form');
$c = Page::getByPath('/dashboard/system/attributes/topics');
$cp = new Permissions($c);
if (!$cp->canViewPage()) {
	exit;
}
$allChildren = $_REQUEST['allChildren'];

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
$result = $tree->getJSON($allChildren);
print Loader::helper('ajax')->sendResult($result);