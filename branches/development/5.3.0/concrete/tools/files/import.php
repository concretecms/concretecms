<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/files");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Unable to access the file manager."));
}
$valt = Loader::helper('validation/token');
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
		post_params: {'ccm-session' : "<?php echo session_id(); ?>",'ccm_token' : '<?=$valt->generate("upload")?>'},
		file_size_limit : "100 MB",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "ccm-file-upload-multiple-progress",
			cancelButtonId : "ccm-file-upload-multiple-btnCancel"
		},
		debug: false,

		// Button settings
		button_image_url: "<?=ASSETS_URL_IMAGES?>/icons/add_file_swfupload.png",	// Relative to the Flash file
		button_width: "16",
		button_height: "16",
		button_placeholder_id: "ccm-file-upload-multiple-spanButtonPlaceHolder",
		
		// The event handler functions are defined in handlers.js
		// wrapped function with apply are so c5 can do anything special it needs to
		// some functions needed to be overridden completly
		file_queued_handler : function (file) {
			fileQueued.apply(this,[file]);
			$('#'+this.customSettings.progressTarget).append('<br style="clear:left"/>');
		},
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : function(numFilesSelected, numFilesQueued){
			try {
				if (numFilesSelected > 0) {
					document.getElementById(this.customSettings.cancelButtonId).disabled = false;
				}								
				//this.startUpload();
			} catch (ex)  {
				this.debug(ex);
			}		
		},
		upload_start_handler : uploadStart,
		upload_progress_handler : function(file, bytesLoaded, bytesTotal){
			try {
				var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		
				var progress = new FileProgress(file, this.customSettings.progressTarget);
				progress.setProgress(percent);
				
				progress.setStatus("Uploading... ("+percent+"%)");
			} catch (ex) {
				this.debug(ex);
			}		
		},
		upload_error_handler : uploadError,
		upload_success_handler : function(file, serverData){
			try {
				eval('serverData = '+serverData);
				var progress = new FileProgress(file, this.customSettings.progressTarget);
				progress.setComplete();
				progress.setStatus(serverData['message']);
				progress.toggleCancel(false);
				if(serverData['id']){
					if(!this.highlight){this.highlight = [];}
					this.highlight.push(serverData['id']);
				}
			} catch (ex) {
				this.debug(ex);
			}		
		},
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete	// Queue plugin event
	});

	
});
</script>

<style type="text/css">

</style>

<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
		<div class="fieldset flash" id="ccm-file-upload-multiple-progress">
		<span class="legend"><?=t('Upload Queue');?></span>
		</div>
		
		<div><div id="ccm-file-upload-multiple-results">0 <?=t('Files Uploaded');?></div></div>
		<br style="clear:left;"/>
		<div>
			<span id="ccm-file-upload-multiple-spanButtonPlaceHolder" style="width:16px;"></span>
			<input id="ccm-file-upload-multiple-btnStart"  type="button" value="<?=t('Start Uploads')?>" onclick="swfu.startUpload();" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
			<input id="ccm-file-upload-multiple-btnCancel" type="button" value="<?=t('Cancel All Uploads')?>" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
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
	<div id="incoming_files" class="incoming_file_importer">
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
			<tr>
				<td style="text-align: center"><input type="checkbox" name="removeFilesAfterPost" value="1" checked /></td>
				<td colspan="2">
				
				<?=t('Remove files from incoming/ directory.')?></td>
				<td>
				<?
					$h = Loader::helper('concrete/interface');
					$b1 = $h->submit(t('Add Files'), 'file_importer_form');
					print $b1;
				?>
				</td>
			</tr>
		</table>
		<div class="clear"></div>
	</div>
	<?=$valt->output('import_incoming');?>
	<div style="clear: both">&nbsp;</div>

</form>
<?php } else { ?>
	<?=t('No files found in %s', DIR_FILES_INCOMING)?>
<?php } ?>
</div>

<div id="ccm-file-add-remote-tab" style="display: none">
<h1><?=t('Add Remote Files')?></h1>
<form method="POST" id="ccm-file-add-remote-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote" target="ccm-upload-more-options-frame<?=$iframeNoCache?>">
	<h3><?=t('Enter URL to valid file(s)')?></h3>
	<?=$form->text('url_upload_1', array('style' => 'width:90%'))?><br/>
	<?=$form->text('url_upload_2', array('style' => 'width:90%'))?><br/>
	<?=$form->text('url_upload_3', array('style' => 'width:90%'))?><br/>
	<?=$form->text('url_upload_4', array('style' => 'width:90%'))?><br/>
	<?=$form->text('url_upload_5', array('style' => 'width:90%'))?><br/>
	<br/>
	<?
		$h = Loader::helper('concrete/interface');
		$b1 = $h->submit(t('Add Files'), 'ccm-file-add-remote-form', 'left');
		print $b1;
	?>
</form>
</div>