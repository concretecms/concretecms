<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-file-manager-search-form" data-header="file-manager">
    <form method="post" action="<?php echo URL::to('/ccm/system/search/files/submit')?>">
        <a class="ccm-file-manager-reset-search" href="#" data-button-action="clear-search"><?=t('Reset Search')?></a>
        <a class="ccm-file-manager-launch-advanced-search" href="<?php echo URL::to('/ccm/system/dialogs/file/advanced_search')?>" data-launch-dialog="advanced-search"><?=t('Advanced')?></a>
        <div class="input-group">

            <input type="text" class="form-control" name="fKeywords" placeholder="<?=t('Search')?>">
              <span class="input-group-btn">'
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
              </span>
        </div><!-- /input-group -->
        <ul class="ccm-file-manager-navigation">
            <li><a href="#" data-launch-dialog="add-file-manager-folder"><i class="fa fa-folder-o"></i> <?=t('New Folder')?></a></li>
            <li><a href="#" id="ccm-file-manager-upload">
                    <i class="fa fa-file"></i> <?=t('Upload Files')?>
                    <input type="file" name="files[]" multiple="multiple" />
                    <input name="ccm_token" value="" type="hidden">
                    <input name="currentFolder" value="" type="hidden">
                </a></li>
            <li><a data-dialog="add-files" href="#">
                    <i class="fa fa-upload"></i> <?=t('More Options')?>
                </a>
            </li>


        </ul>
    </form>
</div>
<div class="clearfix"></div>

<div style="display: none">
    <div data-dialog="add-file-manager-folder" class="ccm-ui">
        <form data-dialog-form="add-folder" method="post" action="<?=$addFolderAction?>">
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