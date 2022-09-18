<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="edit-search-preset" class="form-horizontal" action="<?= $controller->getEditSearchPresetAction(); ?>">
        <?= $token->output('edit_search_preset'); ?>
        <?= $form->hidden('presetID', $searchPreset->getId()); ?>
        <div class="form-group">
            <?= $form->label('presetName', t('Name')); ?>
            <?= $form->text('presetName', $searchPreset->getPresetName()); ?>
        </div>

        <div class="dialog-buttons clearfix">
            <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button class="btn btn-primary float-end" data-dialog-action="submit" type="submit"><?= t('Save Search Preset'); ?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.SavedSearchUpdated');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.SavedSearchUpdated', function(e, data) {
                if (data.form == 'edit-search-preset') {
                    ConcreteEvent.publish('SavedSearchUpdated', { 'preset': data.response });
                }
            });
        });
    </script>

</div>
