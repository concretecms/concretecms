<?php
defined('C5_EXECUTE') or die("Access Denied.");
$node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
$np = new Permissions($node);
if (is_object($node) && $np->canEditTreeNodePermissions()) { ?>

<div class="ccm-ui">
	<?php Loader::element('permission/lists/tree/node', array(
		'node' => $node
	))?>
</div>

<?php
}

