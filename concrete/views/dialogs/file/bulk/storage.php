<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
if (!empty($files)) {

    // let's check the storage locations to see if all the files use the same and check the correct checkbox
    $usedStorageLocations = [];
    $currentStorageLocation = null;
    foreach ($files as $file) {
        $fileStorageLocationID = $file->getStorageLocationID();
        if (!in_array($fileStorageLocationID, $usedStorageLocations)) {
            $usedStorageLocations[] = $fileStorageLocationID;
        }
    }
    if (count($usedStorageLocations) === 1) {
        $currentStorageLocation = $usedStorageLocations[0];
    } ?>

    <form data-dialog-form="bulk-file-storage" method="post" action="<?= $controller->action('submit'); ?>">

        <?php foreach ($files as $file) { ?>
            <input type="hidden" name="fID[]" value="<?= $file->getFileID(); ?>" />
        <?php } ?>

        <div class="ccm-ui">
            <?php foreach ($locations as $fsl) { ?>
                <div class="radio">
                    <label>
                        <?= $form->radio('fslID', $fsl->getID(), $currentStorageLocation); ?> <?= $fsl->getDisplayName(); ?>
                    </label>
                </div>
            <?php } ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?= t('Move Location'); ?></button>
        </div>

    </form>

    <script type="text/javascript">
        $(function() {
            $('form[data-dialog-form=bulk-file-storage]').on('submit', function () {
                var params = $('form[data-dialog-form=bulk-file-storage]').formToArray(true);
                $.concreteAjax({
                    url: '<?= $controller->action('submit'); ?>',
                    data: params,
                    success: function(r) {
                        jQuery.fn.dialog.closeTop();
                        ccm_triggerProgressiveOperation(
                            '<?= $controller->action('change_files_storage_location'); ?>',
                            params,
                            <?= json_encode(t('Change files storage location')); ?>,
                            function (result) {
                                ConcreteEvent.publish('FileManagerBulkFileStorageComplete', {files: result.files});
                                ConcreteAlert.notify({message: <?= json_encode(t('File storage locations updated successfully.')); ?>});
                            }
                        );
                    }
                });
                return false;
            });
        });
    </script>
<?php } ?>