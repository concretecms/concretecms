<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = \Core::make('helper/form');
?>

<div class="ccm-ui">
	<form method="post" data-topic-form="add-category-node" class="form-horizontal" action="<?=$controller->action('add_category_node')?>">
		<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>">
		<?=Loader::helper('validation/token')->output('add_category_node')?>
		<div class="form-group">
			<?=$form->label('treeNodeCategoryName', t('Category Name'))?>
			<?=$form->text('treeNodeCategoryName', '', array('class' => 'span4'))?>
		</div>

		<div class="dialog-buttons">
			<button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
			<button class="btn btn-primary pull-right" type="submit"><?=t('Add')?></button>
		</div>
	</form>
</div>