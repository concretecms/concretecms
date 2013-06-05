<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<? $valt = Loader::helper('validation/token'); ?>
<? if ($mode == 'replace') { ?>


<div id="ccm-files-add-asset-replace">
<h3><?=t('Upload File')?>:</h3>
<form method="post" enctype="multipart/form-data" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" class="ccm-file-manager-submit-single">
    <input type="file" name="Filedata" size="12" class="ccm-al-upload-single-file" />
    <?=$valt->output('upload');?>
    <input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
    <input type="hidden" name="fID" value="<?=$fID?>" />
    <img class="ccm-al-upload-single-loader" style="display:none;" src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input class="ccm-al-upload-single-submit btn" type="submit" value="<?=t('Upload')?>" />    
</form>
</div>

<? } else { 

$form = Loader::helper("form");
$fp = FilePermissions::getGlobal();
if ($fp->canAddFiles()) {

?>

<div id="ccm-files-add-asset" class="clearfix" >
<form method="post" enctype="multipart/form-data" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" class="ccm-file-manager-submit-single">
	<input type="file" name="Filedata" class="ccm-al-upload-single-file"  />
    <input class="ccm-al-upload-single-submit btn" type="submit" value="<?=t('Upload File')?>" />    
	<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/import?ocID=<?=$ocID?>&searchInstance=<?=$searchInstance?>" class="dialog-launch btn info" dialog-title="<?=t('Add Files')?>" dialog-on-close="if(swfu && swfu.highlight) { ccm_alRefresh(swfu.highlight, '<?=$searchInstance?>') }" dialog-modal="false" dialog-width="450" dialog-height="370" dialog-append-buttons="true"><?=t('Upload Multiple')?></a>
	<img class="ccm-al-upload-single-loader" style="display:none;" src="<?=ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" />
<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
<?=$valt->output('upload');?>
<input type="hidden" name="ocID" value="<?=$ocID?>" />
</form>
</div>

<? } 

}
?>