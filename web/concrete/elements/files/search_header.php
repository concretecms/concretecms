<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-file-manager-search-form" data-header="file-manager">
    <form>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="<?=t('Search')?>">
              <span class="input-group-btn">
                <button class="btn btn-info" type="button"><i class="fa fa-search"></i></button>
              </span>
        </div><!-- /input-group -->
        <ul class="ccm-file-manager-navigation">
            <li><a href="#" data-launch-dialog="add-file-manager-folder"><i class="fa fa-folder-o"></i> <?=t('New Folder')?></a></li>
            <li><a href="#" class="ccm-file-manager-upload-link"><i class="fa fa-file"></i> <?=t('Upload Files')?></a></li>
        </ul>
    </form>
</div>
<div class="clearfix"></div>

<div style="display: none">
    <div data-dialog="add-file-manager-folder" class="ccm-ui">
        <form data-dialog-form="add-folder" method="post" class="form-stacked" action="<?=$addFolderAction?>">
            <?=$token->output('add_folder')?>
            <?=$form->hidden('currentFolder', $currentFolder);?>
            <div class="form-group">
                <?=$form->label('folderName', t('Folder Name'))?>
                <?=$form->text('folderName')?>
            </div>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
            <button class="btn btn-primary pull-right" data-dialog-action="submit"><?=t('Add Folder')?></button>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(function() {
        $('div[data-header=file-manager]').concreteFileManagerHeader();
    });
</script>