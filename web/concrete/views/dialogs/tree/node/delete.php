<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
    <form method="post" data-tree-form="remove-tree-node" class="form-horizontal" action="<?=$controller->action('remove_tree_node')?>">
        <?=Loader::helper('validation/token')->output('remove_tree_node')?>
        <input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
        <p><?=t('Are you sure you want to remove this node?')?></p>

        <div class="dialog-buttons">
            <button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
            <button class="btn btn-danger pull-right" type="submit"><?=t('Remove Node')?></button>
        </div>
    </form>
</div>
