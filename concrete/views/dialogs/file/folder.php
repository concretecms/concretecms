<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" data-dialog-form="move-to-folder" action="<?=$controller->action('submit')?>">

    <div class="ccm-ui">
        <?php Loader::element('files/move_to_folder', array(
            'displayFolder' => function ($folder) use ($f) {
                $fp = \FilePermissions::getGlobal();
                if (!$fp->canAddFiles() || !$fp->canAddFileType(strtolower($f->getExtension()))) {
                    return false;
                } else {
                    $fileFolderObject = $f->getFileFolderObject();
                    if (is_object($fileFolderObject) && $fileFolderObject->getTreeNodeID() === $folder->getTreeNodeID()) {
                        return false;
                    }
                    return true;
                }
            },
            'getRadioButton' => function ($folder, $checked = false) use ($f) {
                $radio = id(new HtmlObject\Input('radio', 'folderID', $folder->getTreeNodeID(), array('checked' => $checked)));
                
                return $radio;
            },
        ));?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
    </div>

</form>
<script type="text/javascript">
    $(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateFolder');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateFolder', function(e, data) {
            if (data.form == 'move-to-folder') {
                ConcreteEvent.publish('FolderUpdateRequestComplete', {
                    'folder': data.response.folder
                });
            }
        });

    });
</script>