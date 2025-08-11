<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="edit-file-folder-node" class="form-horizontal" action="<?= $controller->action('update_file_folder_node'); ?>">
        <?= $validation_token->output('update_file_folder_node'); ?>
        <input type="hidden" name="treeNodeID" value="<?= $node->getTreeNodeID(); ?>" />
        <div class="form-group">
            <?= $form->label('fileFolderName', t('Name')); ?>
            <?= $form->text('fileFolderName', $node->getTreeNodeName()); ?>
        </div>
        <div class="form-group">
            <?= $form->label('fileFolderFileStorageLocation', t('Storage Location')); ?>
            <?= $form->select('fileFolderFileStorageLocation', $locations, $node->getTreeNodeStorageLocationID()); ?>
        </div>
        <div class="dialog-buttons clearfix">
            <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button class="btn btn-primary float-end" data-dialog-action="submit" type="submit"><?= t('Update'); ?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            _.defer(function() {
                $('input[name=fileFolderName]').focus();
            });
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateTreeNode');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateTreeNode', function(e, data) {
                if (data.form == 'edit-file-folder-node') {
                    ConcreteEvent.publish('ConcreteTreeUpdateTreeNode', {'node': data.response});
                }
            });
        });
    </script>
</div>
