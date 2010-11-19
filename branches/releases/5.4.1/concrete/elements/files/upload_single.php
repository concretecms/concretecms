<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php  $valt = Loader::helper('validation/token'); ?>
<?php  if ($mode == 'replace') { ?>


<div id="ccm-files-add-asset-replace">
<h3><?php echo t('Upload File')?>:</h3>
<form method="post" enctype="multipart/form-data" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" class="ccm-file-manager-submit-single">
    <input type="file" name="Filedata" class="ccm-al-upload-single-file" />
    <?php echo $valt->output('upload');?>
    <input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
    <input type="hidden" name="fID" value="<?php echo $fID?>" />
    <img class="ccm-al-upload-single-loader" style="display:none;" src="<?php echo ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input class="ccm-al-upload-single-submit" type="submit" value="<?php echo t('Upload')?>" />    
</form>
</div>

<?php  } else { 

$form = Loader::helper("form");
$fp = FilePermissions::getGlobal();
if ($fp->canAddFiles()) {

?>

<div id="ccm-files-add-asset">
<h3><?php echo t('Add')?>:</h3>
<form method="post" enctype="multipart/form-data" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single" class="ccm-file-manager-submit-single">
    <input type="file" name="Filedata" class="ccm-al-upload-single-file"  />
    <input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
    <?php echo $valt->output('upload');?>
    <input type="hidden" name="ocID" value="<?php echo $ocID?>" />
    <img class="ccm-al-upload-single-loader" style="display:none;" src="<?php echo ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" />
    <input class="ccm-al-upload-single-submit" type="submit" value="<?php echo t('Upload')?>" />    
</form>

<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/import?ocID=<?php echo $ocID?>&searchInstance=<?php echo $searchInstance?>" class="dialog-launch" dialog-title="<?php echo t('Add Files')?>" dialog-on-close="if(swfu && swfu.highlight) { ccm_alRefresh(swfu.highlight, '<?php echo $searchInstance?>') }" dialog-modal="false" dialog-width="450" dialog-height="350"><strong><?php echo t('Upload Multiple')?></strong></a>
</div>

<?php  } 

}
?>