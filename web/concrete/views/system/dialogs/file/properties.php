<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
<?
$tabs = array(array('details', t('Details'), true));
if (!$previewMode) {
	$tabs[] = array('versions', t('Versions'));
}
$tabs[] = array('statistics', t('Statistics'));

print Loader::helper('concrete/interface')->tabs($tabs); ?>

<div class="ccm-tab-content" id="ccm-tab-content-details" data-container="editable-fields">

<? if (!$previewMode && $fp->canEditFileContents()) { ?>
	<button class="btn pull-right btn-default btn-xs" data-action="rescan" type="button"><?=t('Rescan')?></button>
<? } ?>

<h4><?=t('Basic Properties')?></h4>
<table border="0" cellspacing="0" cellpadding="0" class="table">
<tr>
	<td><strong><?=t('ID')?></strong></td>
	<td width="100%"><?=$fv->getFileID()?> <span style="color: #afafaf">(<?=t('Version')?> <?=$fv->getFileVersionID()?>)</span></td>
</tr>
<tr>
	<td><strong><?=t('Filename')?></strong></td>
	<td width="100%"><?=$fv->getFileName()?></td>
</tr>
<tr>
	<td><strong><?=t('URL to File')?></strong></td>
	<td width="100%"><?=$fv->getRelativePath(true)?></td>
</tr>
<?
$oc = $f->getOriginalPageObject();
if (is_object($oc)) { 
	$fileManager = Page::getByPath('/dashboard/files/search'); 
	$ocName = $oc->getCollectionName();
	if (is_object($fileManager) && !$fileManager->isError()) {
		if ($fileManager->getCollectionID() == $oc->getCollectionID()) {
			$ocName = t('Dashboard File Manager');
		}
	}
	?>

<tr>
	<td><strong><?=t('Page Added To')?></strong></td>
	<td width="100%"><a href="<?=Loader::helper('navigation')->getLinkToCollection($oc)?>" target="_blank"><?=$ocName?></a></td>
</tr>
<? } ?>

<tr>
	<td><strong><?=t('Type')?></strong></td>
	<td><?=$fv->getType()?></td>
</tr>
<tr>
	<td><strong><?=t('Size')?></strong></td>
	<td><?=$fv->getSize()?> (<?=t2(/*i18n: %s is a number */ '%s byte', '%s bytes', $fv->getFullSize(), Loader::helper('number')->format($fv->getFullSize()))?>)</td>
</tr>
<tr>
	<td><strong><?=t('Date Added')?></strong></td>
	<td><?=t('Added by <strong>%s</strong> on %s', $fv->getAuthorName(), $dateHelper->date(DATE_APP_FILE_PROPERTIES, strtotime($f->getDateAdded())))?></td>
</tr>
<?
Loader::model("file_storage_location");
$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
if (is_object($fsl)) {
	if ($f->getStorageLocationID() > 0) {
		$sli = $fsl->getName() . ' <span style="color: #afafaf">(' . $fsl->getDirectory() . ')</span>';;
	}
}

if (!isset($sli)) {
	$sli = t('Default Location') . ' <span style="color: #afafaf">(' . DIR_FILES_UPLOADED . ')</span>';
}

?>
<tr>
	<td><strong><?=t('Location')?></strong></td>
	<td><?=$sli?></td>
</tr>
<tr>
	<td><strong><?=t('Title')?></strong></td>
	<td><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="text" data-name="fvTitle"<? } ?>><?=$fv->getTitle()?></span></td>
</tr>
<tr>
	<td><strong><?=t('Description')?></strong></td>
	<td><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="textarea" data-name="fvDescription"<? } ?>><?=$fv->getDescription()?></span></td>
</tr>
<tr>
	<td><strong><?=t('Tags')?></strong></td>
	<td><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="textarea" data-name="fvTags"<? } ?>><?=$fv->getTags()?></span></td>
</tr>

</table>

</div>

</div>

<script type="text/javascript">
$(function() {

	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		url: '<?=$controller->action('save')?>'
	});
});
</script>