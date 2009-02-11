<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

if ($_POST['task'] == 'delete_files') {
	foreach($_POST['fID'] as $fID) {
		$f = File::getByID($fID);
		$f->delete();
	}

	$json['error'] = false;
	$js = Loader::helper('json');
	print $js->encode($json);
	exit;
}

$form = Loader::helper('form');
?>

<h1>Delete Files</h1>

<?

	$f = File::getByID($_REQUEST['fID']);
	$fv = $f->getApprovedVersion();
	
	?>

	<?=t('Are you sure you want to delete the file(s): ')?><br/>

	<form id="ccm-delete-files-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete">
	<?=$form->hidden('task', 'delete_files')?>
	<table id="ccm-file-list" border="0" cellspacing="0" cellpadding="0">
	
		<? if (is_object($fv)) { ?>
		
		<?=$form->hidden('fID[]', $f->getFileID())?>		
		
		<tr class="" style="font-weight: bold">
			<td class="ccm-file-list-thumbnail">
				<div class="ccm-file-list-thumbnail-image" fID="<?=$f->getFileID()?>"><?=$fv->getThumbnail(1)?></div>
				<? if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?=$f->getFileID()?>hoverThumbnail"><div><?=$fv->getThumbnail(2)?></div></div>
			<? } ?>
				</td>
			<td><?=$fv->getType()?></td>
			<td class="ccm-file-list-filename"><?=wordwrap($fv->getTitle(), 25, "\n", true)?></td>
			<td><?=date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?=$fv->getSize()?></td>
			<td><?=$fv->getAuthorName()?></td>
		</tr>
		
		<? } ?>

	</table>
	</form>
	<br/>
	<? $ih = Loader::helper('concrete/interface')?>
	<?=$ih->button_js(t('Delete'), 'ccm_alDeleteFiles()')?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
		
		
	<?
	
	