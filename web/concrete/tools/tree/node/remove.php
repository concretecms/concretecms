<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
$np = new Permissions($node);
$tree = $node->getTreeObject();
$url = View::url('/dashboard/system/attributes/topics', 'remove_tree_node');

if (is_object($node) && $np->canDeleteTreeNode() && $tree->getTreeTypeHandle() == 'topic') { ?>

	<div class="ccm-ui">
		<form method="post" data-topic-form="remove-tree-node" class="form-horizontal" action="<?=$url?>">
			<?=Loader::helper('validation/token')->output('remove_tree_node')?>
			<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
			<p><?=t('Are you sure you want to remove this node? It will not remove any resources from the system but it will remove all subcategories and their catalog resource references.')?></p>

			<div class="dialog-buttons">
				<button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
				<button class="btn btn-danger pull-right" type="submit"><?=t('Remove Node')?></button>
			</div>
		</form>
	</div>


<?php
}

