<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/files");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Unable to access the file manager."));
}

?>
<ul class="ccm-dialog-tabs" id="ccm-file-import-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-upload-multiple"><?=t('Upload Multiple')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add Incoming')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
</ul>

<? $iframeNoCache = time(); ?>
<iframe src="" style="display: none" border="0" id="ccm-upload-more-options-frame<?=$iframeNoCache?>" name="ccm-upload-more-options-frame<?=$iframeNoCache?>"></iframe>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.handlers.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.fileprogress.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.queue.js"></script>

<script type="text/javascript">
var ccm_fiActiveTab = "ccm-file-upload-multiple";

$("#ccm-file-import-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_fiActiveTab + "-tab").hide();
	ccm_fiActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_fiActiveTab + "-tab").show();
});

</script>

<div id="ccm-file-upload-multiple-tab">
<h1>Upload Multiple Files</h1>

<script type="text/javascript">

var swfu;
$(function() { 

	swfu = new SWFUpload({

		flash_url : "<?=ASSETS_URL_FLASH?>/swfupload/swfupload.swf",
		upload_url : "<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/multiple",
		post_params: {'ccm-session' : "<?php echo session_id(); ?>"},
		file_size_limit : "100 MB",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: true,

		// Button settings
		button_image_url: "<?=ASSETS_URL_IMAGES?>/icons/add_file.png",	// Relative to the Flash file
		button_width: "16",
		button_height: "16",
		button_placeholder_id: "spanButtonPlaceHolder",
		
		// The event handler functions are defined in handlers.js
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete	// Queue plugin event
	});

	
});
</script>


<form id="form1" action="index.php" method="post" enctype="multipart/form-data">

		<div class="fieldset flash" id="fsUploadProgress">
		<span class="legend">Upload Queue</span>
		</div>
	<div id="divStatus">0 Files Uploaded</div>
		<div>
			<span id="spanButtonPlaceHolder"></span>
			<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
		</div>

</form>

</div>

<?php
	$valt = Loader::helper('validation/token');
	$ch = Loader::helper('concrete/file');
	$fh = Loader::helper('validation/file');
	Loader::library('file/types');
	
	$incoming_contents = $ch->getIncomingDirectoryContents();
?>
<div id="ccm-file-add-incoming-tab" style="display: none">
<h1>Add Files from Incoming Directory</h1>
<?php if(!empty($incoming_contents)) { ?>
<form target="ccm-upload-more-options-frame<?=$iframeNoCache?>" id="file_importer_form" name="file_importer_form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
		<table id="incoming_file_table" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="10%" valign="middle" class="center theader"><input type="checkbox" id="check_all_imports" name="check_all_imports" onclick="toggleCheckboxStatus(document.file_importer_form);" value="" /></td>
				<td width="20%" valign="middle" class="center theader"></td>
				<td width="45%" valign="middle" class="theader"><?=t('Filename')?></td>
				<td width="25%" valign="middle" class="center theader"><?=t('Size')?></td>
			</tr>
		</table>
	<div id="incoming_files" class="incoming_file_importer borderflow">
		<table id="incoming_file_table" width="100%">
		<?php foreach($incoming_contents as $filenum=>$file_array) { 
				$ft = FileTypeList::getType($file_array['name']);
		?>
			<tr>
				<td width="10%" valign="middle" class="center">
					<?php if($fh->extension($file_array['name'])) { ?>
						<input type="checkbox" name="send_file<?=$filenum?>" value="<?=$file_array['name']?>" />
					<?php } ?>
				</td>
				<td width="20%" valign="middle" class="center"><?=$ft->getThumbnail(1)?></td>
				<td width="45%" valign="middle"><?=$file_array['name']?></td>
				<td width="25%" valign="middle" class="center"><?=$file_array['size']?><?=t('Kb')?></div>
			</tr>
		<?php } ?>
		</table>
		<div class="clear"></div>
	</div>
	<?=$valt->output('import_incoming');?>
	<input type="submit" value="Submit" />
</form>
<?php } else { ?>
No Incoming Files Found
<?php } ?>
</div>

<div id="ccm-file-add-remote-tab" style="display: none">
<h1>Add Remote Files</h1>
Form Here
</div>

