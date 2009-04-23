<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}

if ($_POST['task'] == 'delete_files') {
	$json['error'] = false;
	
	if (is_array($_POST['fID'])) {
		foreach($_POST['fID'] as $fID) {
			$f = File::getByID($fID);
			$fp = new Permissions($f);
			if ($fp->canAdmin()) {
				$f->delete();
			} else {
				$json['error'] = t('Unable to delete one or more files.');
			}
		}
	}

	$js = Loader::helper('json');
	print $js->encode($json);
	exit;
}

$form = Loader::helper('form');

$files = array();
if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$files[] = File::getByID($fID);
	}
} else {
	$files[] = File::getByID($_REQUEST['fID']);
}

$fcnt = 0;
foreach($files as $f) { 
	$fp = new Permissions($f);
	if ($fp->canAdmin()) {
		$fcnt++;
	}
}
?>

<h1><?php echo t('Delete Files')?></h1>

<?php  if ($fcnt == 0) { ?>
	<?php echo t("You do not have permission to delete any of the selected files."); ?>
<?php  } else { ?>

	<?php echo t('Are you sure you want to delete the following files?')?><br/><br/>

	<form id="ccm-delete-files-form" method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete">
	<?php echo $form->hidden('task', 'delete_files')?>
	<table id="ccm-file-list" border="0" cellspacing="0" cellpadding="0">
	
	<?php  foreach($files as $f) { 
		$fp = new Permissions($f);
		if ($fp->canAdmin()) {
			$fv = $f->getApprovedVersion();
			if (is_object($fv)) { ?>
			
			<?php echo $form->hidden('fID[]', $f->getFileID())?>		
			
			<tr class="" style="font-weight: bold">
				<td><div class="ccm-file-list-thumbnail"><div class="ccm-file-list-thumbnail-image" fID="<?php echo $f->getFileID()?>"><?php echo $fv->getThumbnail(1)?></div></div></td>
	
				<td><?php echo $fv->getType()?></td>
				<td class="ccm-file-list-filename"><?php echo wordwrap($fv->getTitle(), 25, "\n", true)?></td>
				<td><?php echo date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
				<td><?php echo $fv->getSize()?></td>
				<td><?php echo $fv->getAuthorName()?></td>
			</tr>
			
			<?php  }
		}
		
	} ?>
	</table>
	</form>
	<br/>
	<?php  $ih = Loader::helper('concrete/interface')?>
	<?php echo $ih->button_js(t('Delete'), 'ccm_alDeleteFiles()')?>
	<?php echo $ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
		
		
	<?php 
	
}
	
	