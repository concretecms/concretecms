<?php 
defined('C5_EXECUTE') or die("Access Denied.");
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
<?php  } ?>

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

<form method="post" id="ccm-<?php echo $searchInstance?>-update-uploaded-details-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to_complete/">

<div class="ccm-add-files-complete">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%">
	<?php  if (count($_REQUEST['fID']) == 1) { ?>
		<div class="ccm-message"><strong><?php echo t('1 file uploaded successfully.')?></strong></div>
	<?php  } else { ?>
		<div class="ccm-message"><strong><?php echo t('%s files uploaded successfully.', count($_REQUEST['fID']))?></strong></div>
	<?php  } ?>
	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td>
	<?php 
	$ci = Loader::helper('concrete/interface');
	print $ci->submit(t('Save'), 'ccm-' . $searchInstance . '-update-uploaded-details-form', 'left');
	?>
	</td>
</tr>
</table>

</div>

<div class="ccm-spacer">&nbsp;</div>

<?php echo $form->hidden('task', 'update_uploaded_details')?>
<?php  foreach($files as $f) { ?>
	<input type="hidden" name="fID[]" value="<?php echo $f->getFileID();?>" />
<?php  } ?>
<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />

<hr/>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td width="50%" valign="top">
		<h1 style="margin-top: 12px"><?php echo t('File Details')?></h1>
		
		<div id="ccm-file-properties">
		<h2><?php echo t('Basic Properties')?></h2>
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">  
		<tr>
			<th><?php echo t('Title')?></th>
			<td><?php  if (count($files) > 1) { ?><?php echo t('Multiple Files')?><?php  } else { ?><?php echo $form->text('fvTitle', $files[0]->getTitle())?><?php  } ?></td>
		</tr>
		<tr>
			<th><?php echo t('Description')?></th>
			<td><?php echo $form->textarea('fvDescription')?></td>
		</tr>
		<tr>
			<th><?php echo t('Tags')?></th>
			<td><?php echo $form->textarea('fvTags')?></td>
		</tr>
		</table>
		
		<?php  
		
		if (count($attribs) > 0) { ?>
		
		<br/>
		
		<h2><?php echo t('Other Properties')?></h2>
		<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
		<?php 
		foreach($attribs as $at) { ?>
		
		<tr>
			<th><?php echo $at->getAttributeKeyName()?></th>
			<td><?php echo $at->render('form', false, true)?>
		</tr>
		
		<?php  } ?>
		</table>
		<?php  } ?>
		
		</div>

	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td valign="top">
	
	<div class="ccm-files-add-to-sets-wrapper"><?php  Loader::element('files/add_to_sets', array('disableForm' => true)) ?></div>
	<br/>	
	<div class="ccm-note"><?php echo t('You can assign multiple sets to help find these files later.')?></div>


	</td>
</tr>
</table>

</div>
</form>

<script type="text/javascript">
$(function() {
	ccm_alSetupUploadDetailsForm('<?php echo $searchInstance?>');
});
</script>

<?php 
if (!isset($_REQUEST['reload'])) { ?>
</div>
<?php  }
