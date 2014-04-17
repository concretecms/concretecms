<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ch = Loader::helper('concrete/file');
$h = Loader::helper('concrete/ui');
$form = Loader::helper('form');

$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(t("Unable to add files."));
}

$types = $fp->getAllowedFileExtensions();
$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
$ocID = 0;
if (Loader::helper('validation/numbers')->integer($_REQUEST['ocID'])) {
	$ocID = $_REQUEST['ocID'];
}

$types = $ch->serializeUploadFileExtensions($types);
$valt = Loader::helper('validation/token');
?>
<div class="ccm-ui">
<ul class="nav nav-tabs" id="ccm-file-import-tabs">
<li class="active"><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add Incoming')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
</ul>

<script type="text/javascript">
var ccm_fiActiveTab = "ccm-file-add-incoming";
$("#ccm-file-import-tabs a").click(function() {

	$("li.active").removeClass('active');
	var activesection = ccm_fiActiveTab.substring(13);
	var wind = $(this).parentsUntil('.ui-dialog').parent();
	var bp = wind.find('.ui-dialog-buttonpane');
	$("#dialog-buttons-" + activesection).html(bp.html());

	$("#" + ccm_fiActiveTab + "-tab").hide();
	ccm_fiActiveTab = $(this).attr('id');

	$(this).parent().addClass("active");
	$("#" + ccm_fiActiveTab + "-tab").show();
	var section = $(this).attr('id').substring(13);

	var buttons = $("#dialog-buttons-" + section);
	bp.html(buttons.html());

});
</script>

<div class="help-block" style="margin-top: 11px">
<?=t('Upload Max: %s.', ini_get('upload_max_filesize'))?>
<?=t('Post Max: %s', ini_get('post_max_size'))?>
</div>


<?php
	$valt = Loader::helper('validation/token');
	$fh = Loader::helper('validation/file');
	
	$incoming_contents = $ch->getIncomingDirectoryContents();
?>
<div id="ccm-file-add-incoming-tab" style="display: none">
<h3><?=t('Add from Incoming Directory')?></h3>
<?php if(!empty($incoming_contents)) { ?>
<form id="ccm-file-manager-multiple-incoming" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
	<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
    <input type="hidden" name="ocID" value="<?=$ocID?>" />
		<table id="incoming_file_table" class="table table-bordered" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<th width="10%" valign="middle" class="center theader"><input type="checkbox" id="check_all_imports" name="check_all_imports" onclick="ccm_alSelectMultipleIncomingFiles(this);" value="" /></th>
				<th width="20%" valign="middle" class="center theader"></th>
				<th width="45%" valign="middle" class="theader"><?=t('Filename')?></th>
				<th width="25%" valign="middle" class="center theader"><?=t('Size')?></th>
			</tr>
		<?php foreach($incoming_contents as $filenum=>$file_array) { 
				$ft = FileTypeList::getType($file_array['name']);
		?>
			<tr>
				<td width="10%" valign="middle" class="center">
					<?php if($fh->extension($file_array['name'])) { ?>
						<input type="checkbox" name="send_file<?=$filenum?>" class="ccm-file-select-incoming" value="<?=$file_array['name']?>" />
					<?php } ?>
				</td>
				<td width="20%" valign="middle" class="center"><?=$ft->getThumbnail(1)?></td>
				<td width="45%" valign="middle"><?=$file_array['name']?></td>
				<td width="25%" valign="middle" class="center"><?=Loader::helper('number')->formatSize($file_array['size'], 'KB')?></td>
			</tr>
		<?php } ?>
		</table>
		<input type="checkbox" name="removeFilesAfterPost" value="1" />
		<?=t('Remove files from incoming/ directory.')?>
		
		
	<?=$valt->output('import_incoming');?>

</form>
<?php } else { ?>
	<?=t('No files found in %s', DIR_FILES_INCOMING)?>
<?php } ?>
</div>

<div id="ccm-file-add-remote-tab" style="display: none">
<h3><?=t('Add From Remote URL')?></h3>
<form method="POST" id="ccm-file-manager-multiple-remote" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
	<input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
    <input type="hidden" name="ocID" value="<?=$ocID?>" />
	<p><?=t('Enter URL to valid file(s)')?></p>
	<?=$valt->output('import_remote');?>

	<?=$form->text('url_upload_1', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_2', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_3', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_4', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_5', array('style' => 'width:455px'))?><br/>
</form>
</div>
</div>
