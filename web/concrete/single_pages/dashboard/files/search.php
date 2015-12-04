<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
$fp = FilePermissions::getGlobal();
$imageresize = Config::get('concrete.file_manager.restrict_uploaded_image_sizes');
if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>

<div class="ccm-dashboard-content-full" data-search="files">
<? Loader::element('files/search', array('controller' => $searchController))?>
</div>

    <? if ($fp->canAddFile()) { ?>
	<div id="ccm-file-manager-upload-prompt" class="ccm-file-manager-upload" data-image-resize="<?=($imageresize ? '1' : '0')?>">
        <?=t("<strong>Upload Files</strong> / Click to Choose or Drag &amp; Drop. / ")?>
        <a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/import"
           class="dialog-launch"
           dialog-width="500"
            dialog-height="500"
            dialog-modal="true"
            dialog-title="<?=t('Add Files')?>"><?=t('More Options')?></a>
        <input type="file" name="files[]" multiple="multiple" /></div>
<? } ?>

<? } else { ?>
	<p><?=t("You do not have access to the file manager.");?></p>
<? } ?>
