<? $valt = Loader::helper('validation/token'); ?>
<div id="ccm-files-add-asset">
<label><?=t('Add File')?>:</label>
<form method="post" enctype="multipart/form-data" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single.php" target="_blank" onsubmit="ccm_alSubmitSingle();">
    <input type="file" name="Filedata" id="#ccm-al-upload-single-file" />
    <?=$valt->output('upload');?>
    <img id="ccm-al-upload-single-loader" style="display:none;" src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input id="ccm-al-upload-single-submit" type="submit" value="<?=t('Upload')?>" />    
</form>
</div>
