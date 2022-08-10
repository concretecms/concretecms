<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
    <?php
    /** @var \Concrete\Core\Tree\Node\Type\ExpressEntryCategory|\Concrete\Core\Tree\Node\Type\ExpressEntryResults $node */
    $node->populateChildren();
    $childCount = count($node->getChildNodes());
    if ($childCount > 0) { ?>
        <div class="alert alert-danger">
            <?=t('This results folder contains one or more results folders. You may not remove it until it is empty.')?>
        </div>
    <?php } else if ($node instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {?>
        <div class="alert alert-danger">
            <?=t('This results folder is currently active. You can not remove it.')?>
        </div>
    <?php } else { ?>

        <form method="post" data-dialog-form="remove-tree-node" class="form-horizontal" action="<?=$controller->action('remove_tree_node')?>">
            <?=Loader::helper('validation/token')->output('remove_tree_node')?>
            <input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
            <p><?=t('Are you sure you want to remove the Express entity results folder "%s"?', $node->getTreeNodeDisplayName())?></p>

            <div class="dialog-buttons clearfix">
                <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
                <button class="btn btn-danger float-end" data-dialog-action="submit" type="submit"><?=t('Remove')?></button>
            </div>
        </form>

        <script type="text/javascript">
            $(function() {
                ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.deleteTreeNode');
                ConcreteEvent.subscribe('AjaxFormSubmitSuccess.deleteTreeNode', function(e, data) {
                    if (data.form == 'remove-tree-node') {
                        ConcreteEvent.publish('ConcreteTreeDeleteTreeNode', {'node': data.response});
                    }
                });
            });
        </script>

    <?php } ?>

</div>
