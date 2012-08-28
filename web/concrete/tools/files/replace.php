<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ch = Loader::helper('concrete/file');
$valt = Loader::helper('validation/token');
$form = Loader::helper('form');


$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
	die(t('Access Denied.'));
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
?>

<div class="ccm-ui">

<?

Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'mode' => 'replace', 'fID' => $f->getFileID())); 

?>

<hr />

<h3><?=t('Add from Incoming Directory')?></h3>
<div>
<?
$contents = array();
$con1 = $ch->getIncomingDirectoryContents();
foreach($con1 as $con) {
	$contents[$con['name']] = $con['name'];
}
if (count($contents) > 0) { ?>
<form method="post" id="ccm-file-manager-replace-incoming" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
    <input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
	<?= $form->select('send_file', $contents, array('style' => 'width:200px'));?>
	&nbsp;&nbsp;
	<?= $form->submit('submit', t('Add File')); ?>
	<?= $form->hidden('fID', $f->getFileID()); ?>
	<?=$valt->output('import_incoming');?>
</form>
<? } else { ?>
	<?=t('No files found in %s', DIR_FILES_INCOMING)?>
<? } ?>
</div>

<hr />

<h3><?=t("Add from Remote URL")?></h3>


<form method="post" id="ccm-file-manager-replace-remote" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
<?=$valt->output('import_remote');?>
    <input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
<?= $form->hidden('fID', $f->getFileID()); ?>

<?=$form->text('url_upload_1', array('style' => 'width:195px'))?>
&nbsp;&nbsp;
<?= $form->submit('submit', t('Add File')); ?>

</form>
</div>

<script type="text/javascript">
$(function() { 
	ccm_alSetupSingleUploadForm();
	$("#ccm-file-manager-replace-incoming").submit(function() {
		$(this).attr('target', ccm_alProcessorTarget);		
	});
	$("#ccm-file-manager-replace-remote").submit(function() {
		$(this).attr('target', ccm_alProcessorTarget);		
	});

});

</script>
