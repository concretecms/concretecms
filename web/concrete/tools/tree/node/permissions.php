<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
$np = new Permissions($node);
if (is_object($node) && $np->canEditTreeNodePermissions()) { ?>

<div class="ccm-ui">
	<? Loader::element('permission/lists/tree/node', array(
		'node' => $node
	))?>
</div>

<?
}

