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
	<p>This page demonstrates a simple usage of SWFUpload.  It uses the Queue Plugin to simplify uploading or cancelling all queued files.</p>

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
	Loader::library('file/types');
	
	$incoming_contents = $ch->getIncomingDirectoryContents();
?>
<div id="ccm-file-add-incoming-tab" style="display: none">
<h1>Add Files from Incoming Directory</h1>
<?php if(!empty($incoming_contents)) { ?>
<div class="incoming_file_importer">
	<div class="incoming_file leftside"><input type="checkbox" id="check_all_imports" name="check_all_imports" onclick="toggleCheckboxStatus(document.file_importer_form.send_file);" value="" /></div>
	<div class="incoming_file center"><strong>Filename</strong></div>
	<div class="incoming_file rightside"><strong>Size</strong></div>
	<div class="clear">		
</div>
<form target="ccm-upload-more-options-frame<?=$iframeNoCache?>" id="file_importer_form" name="file_importer_form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
	<div id="incoming_files" class="incoming_file_importer height borderflow">
	<?php foreach($incoming_contents as $filenum=>$file_array) { 
		$ft = FileTypeList::getType($file_array['name']);
		?>
		<div class="incoming_file_thumbnail"><?=$ft->getThumbnail(1)?></div>
		<div class="incoming_file leftside"><input type="checkbox" name="send_file<?=$filenum?>" value="<?=$file_array['name']?>" /></div>
		<div class="incoming_file center"><?=$file_array['name']?></div>
		<div class="incoming_file rightside"><?=$file_array['size']?>KB</div>
		<div class="clear"></div>
	<?php } ?>
	</div>
	<div class="clear"></div>
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

