<? $valt = Loader::helper('validation/token'); ?>
<? $iframeNoCache = time(); ?>
<div id="ccm-files-add-asset">
<h3><?=t('Add File')?>:</h3>
<form method="post" enctype="multipart/form-data" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" target="ccm-upload-frame<?=$iframeNoCache?>" onsubmit="ccm_alSubmitSingle();">
    <input type="file" name="Filedata" id="ccm-al-upload-single-file" />
    <?=$valt->output('upload');?>
    <img id="ccm-al-upload-single-loader" style="display:none;" src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input id="ccm-al-upload-single-submit" type="submit" value="<?=t('Upload')?>" />    
</form>
<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/import" class="dialog-launch" dialog-title="<?=t('Add Files')?>" dialog-modal="false" dialog-width="450" dialog-height="350">More Options</a>
</div>
<iframe src="" style="display: none" border="0" id="ccm-upload-frame<?=$iframeNoCache?>" name="ccm-upload-frame<?=$iframeNoCache?>"></iframe>
