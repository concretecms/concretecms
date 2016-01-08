<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
$np = new Permissions($node);
$tree = $node->getTreeObject();
$parent = $node->getTreeNodeParentObject();
$pp = new Permissions($parent);
if (is_object($node) && $pp->canDuplicateTreeNode()) {
	$newnode = $node->duplicate($parent);
	$r = new stdClass;
	$r->treeNodeParentID = $parent->getTreeNodeID();
	Loader::helper("ajax")->sendResult($r);
}