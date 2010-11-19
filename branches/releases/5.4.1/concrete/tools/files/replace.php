<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ch = Loader::helper('concrete/file');
$valt = Loader::helper('validation/token');
$form = Loader::helper('form');


$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canWrite()) {
	die(t('Access Denied.'));
}

$searchInstance = $_REQUEST['searchInstance'];

Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'mode' => 'replace', 'fID' => $f->getFileID())); 

?>

<hr />

<h3><?php echo t('Add from Incoming Directory')?></h3>
<div>
<?php 
$contents = array();
$con1 = $ch->getIncomingDirectoryContents();
foreach($con1 as $con) {
	$contents[$con['name']] = $con['name'];
}
if (count($contents) > 0) { ?>
<form method="post" id="ccm-file-manager-replace-incoming" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
    <input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
	<?php echo  $form->select('send_file', $contents, array('style' => 'width:200px'));?>
	&nbsp;&nbsp;
	<?php echo  $form->submit('submit', t('Add File')); ?>
	<?php echo  $form->hidden('fID', $f->getFileID()); ?>
	<?php echo $valt->output('import_incoming');?>
</form>
<?php  } else { ?>
	<?php echo t('No files found in %s', DIR_FILES_INCOMING)?>
<?php  } ?>
</div>

<hr />

<h3><?php echo t("Add from Remote URL")?></h3>

<form method="post" id="ccm-file-manager-replace-remote" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
<?php echo $valt->output('import_remote');?>
    <input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
<?php echo  $form->hidden('fID', $f->getFileID()); ?>

<?php echo $form->text('url_upload_1', array('style' => 'width:195px'))?>
&nbsp;&nbsp;
<?php echo  $form->submit('submit', t('Add File')); ?>

</form>

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
