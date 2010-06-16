<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
Loader::model("file_attributes");
$previewMode = false;

$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}  

$attribs = FileAttributeKey::getUserAddedList();

$files = array();
$extensions = array();
$file_versions = array();
$searchInstance = $_REQUEST['searchInstance'];


//load all the requested files
if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canWrite()) {
			$files[] = $f;
			$extensions[] = strtolower($f->getExtension()); 
		}
	}
} else {
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canRead()) {
		$files[] = $f;
		$extensions[] = strtolower($f->getExtension()); 
	}
} 

if (count($files) == 0) {
	print t('You do not have access to edit the selected files.');
	exit;
}


if ($_POST['task'] == 'update_uploaded_details') {
	foreach($files as $f) {
		if (count($files) == 1) { 
			$f->updateTitle($_POST['fvTitle']);
		}
		$f->updateDescription($_POST['fvDescription']);
		$f->updateTags($_POST['fvTags']);
		foreach($attribs as $at) { 
			$at->saveAttributeForm($f);
		}
		
	}	

	foreach($_POST as $key => $value) {

		if (preg_match('/fsID:/', $key)) {
			$fsIDst = explode(':', $key);
			$fsID = $fsIDst[1];
			
			// so the affected file set is $fsID, the state of the thing is $value
			$fs = FileSet::getByID($fsID);
			$fsp = new Permissions($fs);
			if ($fsp->canAddFile($f)) {
				switch($value) {
					case '2':
						foreach($files as $f) {
							$fs->addFileToSet($f);
						}
						break;
				}		
			}			
		}
	}

	if ($_POST['fsNew']) {
		$type = ($_POST['fsNewShare'] == 1) ? FileSet::TYPE_PUBLIC : FileSet::TYPE_PRIVATE;
		$fs = FileSet::createAndGetSet($_POST['fsNewText'], $type);
		//print_r($fs);
		foreach($files as $f) {
			$fs->addFileToSet($f);
		}
	}
	
	$js = Loader::helper('json');
	$json = array();
	foreach($files as $f) {
		$json[] = $f->getFileID();
	}
	print $js->encode($json);
	exit;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-file-properties-wrapper">
<? } ?>

<script type="text/javascript">
$(function() {
	$("div.ccm-message").show('highlight');
});
</script>

<style type="text/css">
div.ccm-add-files-complete div.ccm-message {margin-bottom: 0px}
table.ccm-grid input.ccm-input-text, table.ccm-grid textarea {width: 100%}
table.ccm-grid td {padding-right: 20px; width: 230px}
table.ccm-grid th {width: 70px}
</style>

<form method="post" id="ccm-<?=$searchInstance?>-update-uploaded-details-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to_complete/">

<div class="ccm-add-files-complete">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%">
	<? if (count($_REQUEST['fID']) == 1) { ?>
		<div class="ccm-message"><strong><?=t('1 file uploaded successfully.')?></strong></div>
	<? } else { ?>
		<div class="ccm-message"><strong><?=t('%s files uploaded successfully.', count($_REQUEST['fID']))?></strong></div>
	<? } ?>
	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td>
	<?
	$ci = Loader::helper('concrete/interface');
	print $ci->submit(t('Save'), 'ccm-' . $searchInstance . '-update-uploaded-details-form', 'left');
	?>
	</td>
</tr>
</table>

</div>

<div class="ccm-spacer">&nbsp;</div>

<?=$form->hidden('task', 'update_uploaded_details')?>
<? foreach($files as $f) { ?>
	<input type="hidden" name="fID[]" value="<?=$f->getFileID();?>" />
<? } ?>
<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />

<hr/>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td width="50%" valign="top">
		<h1 style="margin-top: 12px"><?=t('File Details')?></h1>
		
		<div id="ccm-file-properties">
		<h2><?=t('Basic Properties')?></h2>
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">  
		<tr>
			<th><?=t('Title')?></th>
			<td><? if (count($files) > 1) { ?><?=t('Multiple Files')?><? } else { ?><?=$form->text('fvTitle', $files[0]->getTitle())?><? } ?></td>
		</tr>
		<tr>
			<th><?=t('Description')?></th>
			<td><?=$form->textarea('fvDescription')?></td>
		</tr>
		<tr>
			<th><?=t('Tags')?></th>
			<td><?=$form->textarea('fvTags')?></td>
		</tr>
		</table>
		
		<? 
		
		if (count($attribs) > 0) { ?>
		
		<br/>
		
		<h2><?=t('Other Properties')?></h2>
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
		<?
		foreach($attribs as $at) { ?>
		
		<tr>
			<th><?=$at->getAttributeKeyName()?></th>
			<td><?=$at->render('form', false, true)?>
		</tr>
		
		<? } ?>
		</table>
		<? } ?>
		
		</div>

	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td valign="top">
	
	<div class="ccm-files-add-to-sets-wrapper"><? Loader::element('files/add_to_sets', array('disableForm' => true)) ?></div>
	<br/>	
	<div class="ccm-note"><?=t('You can assign multiple sets to help find these files later.')?></div>


	</td>
</tr>
</table>

</div>
</form>

<script type="text/javascript">
$(function() {
	ccm_alSetupUploadDetailsForm('<?=$searchInstance?>');
});
</script>

<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? }
