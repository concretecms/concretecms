<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$form = Loader::helper('form');
$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
$np = new Permissions($node);
$tree = $node->getTreeObject();
$canEdit = (is_object($node) && $node->getTreeNodeTypeHandle() == 'topic_category' && $np->canEditTreeNode());
$url = View::url('/dashboard/system/attributes/topics', 'update_category_node');
$al = Loader::helper("concrete/asset_library");
if ($canEdit) { ?>

	<div class="ccm-ui">
		<form method="post" data-topic-form="update-category-node" class="form-horizontal" action="<?=$url?>">
			<?=Loader::helper('validation/token')->output('update_category_node')?>
			<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
			<div class="control-group">
				<?=$form->label('treeNodeCategoryName', t('Category Name'))?>
				<div class="controls">
					<?=$form->text('treeNodeCategoryName', $node->getTreeNodeDisplayName(), array('class' => 'span4'))?>
				</div>
			</div>
			<div class="dialog-buttons">
				<button class="btn" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
				<button class="btn btn-primary pull-right" type="submit"><?=t('Update')?></button>
			</div>
		</form>
	</div>


<?
}

