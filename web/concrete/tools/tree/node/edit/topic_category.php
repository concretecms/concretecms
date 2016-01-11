<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
$np = new Permissions($node);
$tree = $node->getTreeObject();
$canEdit = (is_object($node) && $node->getTreeNodeTypeHandle() == 'topic_category' && $np->canEditTreeNode());
$url = View::url('/dashboard/system/attributes/topics', 'update_category_node');
$al = Loader::helper("concrete/asset_library");
if ($canEdit) {
    ?>

	<div class="ccm-ui">
		<form method="post" data-topic-form="update-category-node" class="form-horizontal" action="<?=$url?>">
			<?=Loader::helper('validation/token')->output('update_category_node')?>
			<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
			<div class="form-group">
				<?=$form->label('treeNodeCategoryName', t('Category Name'))?>
				<?=$form->text('treeNodeCategoryName', $node->getTreeNodeName(), array('class' => 'span4'))?>
			</div>
			<div class="dialog-buttons">
				<button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
				<button class="btn btn-primary pull-right" type="submit"><?=t('Update')?></button>
			</div>
		</form>
	</div>


<?php

}
