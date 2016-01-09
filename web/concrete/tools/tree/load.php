<?php
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Tree\Tree;
use \Concrete\Core\Tree\Node\Node as TreeNode;

$form = Loader::helper('form');
$treeID = Loader::helper('security')->sanitizeInt($_REQUEST['treeID']);
$tree = Tree::getByID($treeID);
if (!is_object($tree)) {
	exit;
}

if (is_array($_REQUEST['treeNodeSelectedIDs'])) {
    $selectedIDs = array();
    foreach($_REQUEST['treeNodeSelectedIDs'] as $nID) {
        $node = TreeNode::getByID($nID);
        if (is_object($node) && $node->getTreeID() == $tree->getTreeID()) {
            $selectedIDs[] = $node->getTreeNodeID();
        }
    }
    $tree->setSelectedTreeNodeIDs($selectedIDs);
}

$tree->setRequest($_REQUEST);
$result = $tree->getJSON();
print Loader::helper('ajax')->sendResult($result);