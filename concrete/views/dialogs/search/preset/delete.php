<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="remove-search-preset" class="form-horizontal" action="<?= $controller->getDeleteSearchPresetAction(); ?>">
        <?= $token->output('remove_search_preset'); ?>
        <?= $form->hidden('presetID', $searchPreset->getId()); ?>
        <p><?= t('Are you sure you want to remove the "%s" search preset?', h($searchPreset->getPresetName())); ?></p>

        <div class="dialog-buttons clearfix">
            <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button class="btn btn-danger float-end" data-dialog-action="submit" type="submit"><?= t('Remove'); ?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.SavedSearchDeleted');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.SavedSearchDeleted', function(e, data) {
                if (data.form == 'remove-search-preset') {
                    ConcreteEvent.publish('SavedSearchDeleted', {'preset': data.response});
                }
            });
        });
    </script>

</div>
