<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="add-page-node" class="form-horizontal" action="<?= $controller->action('add_page_node'); ?>">
        <input type="hidden" name="treeNodeID" value="<?= $node->getTreeNodeID(); ?>">
        <div class="mb-3">
            <label class="form-label"><?=t('Page')?></label>
            <?=Core::make('helper/form/page_selector')->selectPage('pageID', null, [
                    'includeSystemPages' => true
            ])?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?=t('Sub-Pages')?></label>
            <div class="form-check">
                <input type="checkbox" id="includeSubpagesInMenu" name="includeSubpagesInMenu" class="form-check-input" value="1">
                <label class="form-check-label" for="includeSubpagesInMenu"><?=t('Dynamically include sub-pages in menu.')?></label>
            </div>
        </div>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-end" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button class="btn btn-primary float-end" data-dialog-action="submit" type="button"><?= t('Add'); ?></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addTreeNode');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addTreeNode', function(e, data) {
            if (data.form == 'add-page-node') {
                ConcreteEvent.publish('ConcreteTreeAddTreeNode', {'node': data.response});
            }
        });
    });
</script>
