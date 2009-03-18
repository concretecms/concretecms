<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<? $valt = Loader::helper('validation/token'); ?>
<? if ($mode == 'replace') { ?>


<div id="ccm-files-add-asset-replace">
<h3><?=t('Upload File')?>:</h3>
<form method="post" enctype="multipart/form-data" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" class="ccm-file-manager-submit-single">
    <input type="file" name="Filedata" class="ccm-al-upload-single-file" />
    <?=$valt->output('upload');?>
    <input type="hidden" name="fID" value="<?=$fID?>" />
    <img class="ccm-al-upload-single-loader" style="display:none;" src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input class="ccm-al-upload-single-submit" type="submit" value="<?=t('Upload')?>" />    
</form>
</div>

<? } else { 

$fp = FilePermissions::getGlobal();
if ($fp->canAddFiles()) {

?>

<div id="ccm-files-add-asset">
<h3><?=t('Add')?>:</h3>
<form method="post" enctype="multipart/form-data" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" class="ccm-file-manager-submit-single">
    <input type="file" name="Filedata" class="ccm-al-upload-single-file" />
    <?=$valt->output('upload');?>
    <img class="ccm-al-upload-single-loader" style="display:none;" src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input class="ccm-al-upload-single-submit" type="submit" value="<?=t('Upload')?>" />    
</form>
<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/import" class="dialog-launch" dialog-title="<?=t('Add Files')?>" dialog-on-close="if(swfu && swfu.highlight) { ccm_alRefresh(swfu.highlight) }" dialog-modal="false" dialog-width="450" dialog-height="350"><?=t('More')?></a>
</div>

<? } 

}
?>