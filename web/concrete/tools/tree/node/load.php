<?php
defined('C5_EXECUTE') or die("Access Denied.");
$node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
$selected = array();
if (is_array($_REQUEST['treeNodeSelectedIDs'])) {
    foreach($_REQUEST['treeNodeSelectedIDs'] as $nodeID) {
        $selected[] = intval($nodeID);
    }
}

if (is_object($node)) {
	$np = new Permissions($node);
	if ($np->canViewTreeNode()) {
		$node->getTreeObject()->setRequest($_REQUEST);
		$node->populateDirectChildrenOnly();
		$r = array();
		if(count($selected) > 0) {
			foreach($selected as $match) {
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