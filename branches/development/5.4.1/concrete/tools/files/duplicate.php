<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$js = Loader::helper('json');

$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}

if ($_POST['task'] == 'duplicate_multiple_files') {
	$json['error'] = false;
	
	if (is_array($_POST['fID'])) {
		foreach($_POST['fID'] as $fID) {
			$f = File::getByID($fID);
			if ($fp->canAddFileType($f->getExtension())) {
				$f->duplicate();
			} else {
				$json['error'] = t('Unable to copy one or more files.');
			}
		}
	}
	if (!$json['error']) {
		$json['fID'] = $_POST['fID'];
	}
	print $js->encode($json);
	exit;
}


if (!is_array($_REQUEST['fID'])) {

	$obj = new stdClass;
	$obj->message = '';
	$obj->error = 0;

	$f = File::getByID($_REQUEST['fID']);
	if (!is_object($f) || $f->isError()) {
		$obj->error = 1;
		$obj->message = t('Invalid file.');
	} else if (!$fp->canAddFileType($f->getExtension())) {
		$obj->error = 1;
		$obj->message = t('You do not have the ability to add new files of this type.');
	}

	if (!$obj->error) {
		$f->duplicate();
	}
	
	print $js->encode($obj);
	exit;

} else {
	
	$files = array();
	
	foreach($_REQUEST['fID'] as $fID) {
		$files[] = File::getByID($fID);
	}

	$fcnt = 0;
	foreach($files as $f) { 
		$fp = new Permissions($f);
		if ($fp->canAddFileType($f->getExtension())) {
			$fcnt++;
		}
	}
	
	$searchInstance = $_REQUEST['searchInstance'];

	?>
	
	<h1><?=t('Copy Files')?></h1>
	
	<? if ($fcnt == 0) { ?>
		<?=t("You do not have permission to copy any of the selected files."); ?>
	<? } else { ?>
		<?=t('Are you sure you want to copy the following files?')?><br/><br/>
	
		<form id="ccm-<?=$searchInstance?>-duplicate-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/duplicate">
		<?=$form->hidden('task', 'duplicate_multiple_files')?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="ccm-results-list">
		
		<? foreach($files as $f) { 
			$fp = new Permissions($f);
			if ($fp->canAddFileType($f->getExtension())) {
				$fv = $f->getApprovedVersion();
				if (is_object($fv)) { ?>
				
				<?=$form->hidden('fID[]', $f->getFileID())?>		
				
				<tr>
					<td>
					<div class="ccm-file-list-thumbnail">
						<div class="ccm-file-list-thumbnail-image" fID="<?=$f->getFileID()?>"><table border="0" cellspacing="0" cellpadding="0" height="70" width="100%"><tr><td align="center" fID="<?=$f->getFileID()?>" style="padding: 0px"><?=$fv->getThumbnail(1)?></td></tr></table></div>
					</div>
					</td>
			
					<td><?=$fv->getType()?></td>
					<td class="ccm-file-list-filename"><?=wordwrap($fv->getTitle(), 25, "\n", true)?></td>
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
		<?=$ih->button_js(t('Copy'), 'ccm_alDuplicateFiles(\'' . $searchInstance . '\')')?>
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
			
			
		<?
		
	}


}