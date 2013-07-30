<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}

if ($_POST['task'] == 'delete_files') {
	$json['error'] = false;
	
	if (is_array($_POST['fID'])) {
		foreach($_POST['fID'] as $fID) {
			$f = File::getByID($fID);
			$fp = new Permissions($f);
			if ($fp->canDeleteFile()) {
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
	if ($fp->canDeleteFile()) {
		$fcnt++;
	}
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>

<div class="ccm-ui">
<br/>
<? if ($fcnt == 0) { ?>
	<p><?=t("You do not have permission to delete any of the selected files."); ?><p>
<? } else { ?>

	<p><?=t('Are you sure you want to delete the following files?')?></p>

	<form id="ccm-<?=$searchInstance?>-delete-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete">
	<?=$form->hidden('task', 'delete_files')?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="table table-bordered">
	
	<? foreach($files as $f) { 
		$fp = new Permissions($f);
		if ($fp->canDeleteFile()) {
			$fv = $f->getApprovedVersion();
			if (is_object($fv)) { ?>
			
			<?=$form->hidden('fID[]', $f->getFileID())?>		
			
			<tr>
				<td><?=$fv->getType()?></td>
				<td class="ccm-file-list-filename" width="100%"><div style="word-wrap: break-word; width: 150px"><?=$fv->getTitle()?></div></td>
				<td><?=date(DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES, strtotime($f->getDateAdded()))?></td>
				<td><?=$fv->getSize()?></td>
				<td><?=$fv->getAuthorName()?></td>
			</tr>
			
			<? }
		}
		
	} ?>
	</table>
	</form>
	<br/>
	
	<? $ih = Loader::helper('concrete/interface')?>
	<div class="dialog-buttons">
	<?=$ih->button_js(t('Delete'), 'ccm_alDeleteFiles(\'' . $searchInstance . '\')', 'right', 'error')?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
	</div>
	
</div>
		
	<?
	
}
	
	
