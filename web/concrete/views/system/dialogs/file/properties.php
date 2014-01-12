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

<div class="ccm-tab-content container" id="ccm-tab-content-details" data-container="editable-fields">

<section>

<? if (!$previewMode && $fp->canEditFileContents()) { ?>
	<button class="btn pull-right btn-default btn-xs" data-action="rescan" type="button"><?=t('Rescan')?></button>
<? } ?>

<h4><?=t('Basic Properties')?></h4>
<div class="row">
	<div class="col-md-3"><p><?=t('ID')?></p></div>
	<div class="col-md-9"><p><?=$fv->getFileID()?> <span style="color: #afafaf">(<?=t('Version')?> <?=$fv->getFileVersionID()?>)</p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Filename')?></p></div>
	<div class="col-md-9"><p><?=$fv->getFileName()?></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('URL to File')?></p></div>
	<div class="col-md-9"><p><?=$fv->getRelativePath(true)?></p></div>
</div>
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
	<div class="row">
		<div class="col-md-3"><p><?=t('Page Added To')?></p></div>
		<div class="col-md-9"><p><a href="<?=Loader::helper('navigation')->getLinkToCollection($oc)?>" target="_blank"><?=$ocName?></a></p></div>
	</div>
<? } ?>

<div class="row">
	<div class="col-md-3"><p><?=t('Type')?></p></div>
	<div class="col-md-9"><p><?=$fv->getType()?></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Size')?></p></div>
	<div class="col-md-9"><p><?=$fv->getSize()?> (<?=t2(/*i18n: %s is a number */ '%s byte', '%s bytes', $fv->getFullSize(), Loader::helper('number')->format($fv->getFullSize()))?>)</p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Date Added')?></p></div>
	<div class="col-md-9"><p><?=t('Added by <strong>%s</strong> on %s', $fv->getAuthorName(), $dateHelper->date(DATE_APP_FILE_PROPERTIES, strtotime($f->getDateAdded())))?></p></div>
</div>
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
<div class="row">
	<div class="col-md-3"><p><?=t('Location')?></p></div>
	<div class="col-md-9"><p><?=$sli?></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Title')?></p></div>
	<div class="col-md-9"><p><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="text" data-name="fvTitle"<? } ?>><?=$fv->getTitle()?></span></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Description')?></p></div>
	<div class="col-md-9"><p><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="textarea" data-name="fvDescription"<? } ?>><?=$fv->getDescription()?></span></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Tags')?></p></div>
	<div class="col-md-9"><p><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="textarea" data-name="fvTags"<? } ?>><?=$fv->getTags()?></span></p></div>
</div>
</section>

<?
$attribs = FileAttributeKey::getImporterList($fv);
$ft = $fv->getType();

if (count($attribs) > 0) { ?>

<section>
<h4><?=t('%s File Properties', $ft)?></h4>

<?

Loader::element('attribute/editable_list', array(
	'attributes' => $attribs, 
	'object' => $f,
	'saveAction' => $controller->action('update_attribute'),
	'clearAction' => $controller->action('clear_attribute'),
	'permissionsArguments' => $fp->canEditFileProperties(),
	'permissionsCallback' => function($ak, $permissionsArguments) {
		return $permissionsArguments;
	}
));?>

<? } ?>
</section>

<? 
$attribs = FileAttributeKey::getUserAddedList();

if (count($attribs) > 0) { ?>

<section>

<h4><?=t('Other Properties')?></h4>

<? Loader::element('attribute/editable_list', array(
	'attributes' => $attribs, 
	'object' => $f,
	'saveAction' => $controller->action('update_attribute'),
	'clearAction' => $controller->action('clear_attribute'),
	'permissionsArguments' => $fp->canEditFileProperties(),
	'permissionsCallback' => function($ak, $permissionsArguments) {
		return $permissionsArguments;
	}
));?>

</section>

<? } ?>

<section>

<h4><?=t('File Preview')?></h4>

<div style="text-align: center">
<?=$fv->getThumbnail(2)?>
</div>

</section>

</div>

</div>

<script type="text/javascript">
$(function() {

	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		url: '<?=$controller->action('save')?>'
	});
});
</script>