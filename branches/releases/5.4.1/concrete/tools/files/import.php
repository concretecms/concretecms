<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ch = Loader::helper('concrete/file');
$h = Loader::helper('concrete/interface');
$form = Loader::helper('form');

$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(_("Unable to add files."));
}

$types = $fp->getAllowedFileExtensions();
$searchInstance = $_REQUEST['searchInstance'];
$ocID = $_REQUEST['ocID'];
$types = $ch->serializeUploadFileExtensions($types);
$valt = Loader::helper('validation/token');
?>
<ul class="ccm-dialog-tabs" id="ccm-file-import-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-upload-multiple"><?php echo t('Upload Multiple')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-incoming"><?php echo t('Add Incoming')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-remote"><?php echo t('Add Remote Files')?></a></li>
</ul>

<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.handlers.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.fileprogress.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.queue.js"></script>

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

<?php 
$umf = ini_get('upload_max_filesize');
$umf = str_ireplace(array('M', 'K', 'G'), array(' MB', 'KB', ' GB'), $umf);
?>

<script type="text/javascript">

var swfu;
$(function() { 

	$("#ccm-file-manager-multiple-remote").submit(function() {
		$(this).attr('target', ccm_alProcessorTarget);		
	});

	$("#ccm-file-manager-multiple-incoming").submit(function() {
		$(this).attr('target', ccm_alProcessorTarget);		
	});

	swfu = new SWFUpload({

		flash_url : "<?php echo ASSETS_URL_FLASH?>/swfupload/swfupload.swf",
		upload_url : "<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/multiple",
		post_params: {'ccm-session' : "<?php  echo session_id(); ?>",'searchInstance': '<?php echo $searchInstance?>', 'ocID' : '<?php echo $ocID?>', 'ccm_token' : '<?php echo $valt->generate("upload")?>'},
		file_size_limit : "<?php echo $umf?>",
		file_types : "<?php echo $types?>",
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		file_types_description : "All Files",
		file_upload_limit : 100,
		button_cursor: SWFUpload.CURSOR.HAND,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "ccm-file-upload-multiple-list",
			cancelButtonId : "ccm-file-upload-multiple-btnCancel"
		},
		debug: false,

		// Button settings
		button_image_url: "<?php echo ASSETS_URL_IMAGES?>/icons/add_file_swfupload.png",	// Relative to the Flash file
		button_width: "80",
		button_text: '<span class="uploadButtonText"><?php echo t('Add Files')?><\/span>',
		button_height: "16",
		button_text_left_padding: 18,
		button_text_style: ".uploadButtonText {background-color: #eee; font-family: Helvetica Neue, Helvetica, Arial}",
		button_placeholder_id: "ccm-file-upload-multiple-spanButtonPlaceHolder",
		
		// The event handler functions are defined in handlers.js
		// wrapped function with apply are so c5 can do anything special it needs to
		// some functions needed to be overridden completly
		file_queued_handler : function (file) {
			fileQueued.apply(this,[file]);
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
				if (serverData['error'] == true) {
					progress.setError(serverData['message']);
				} else {
					progress.setComplete();
				}
				progress.toggleCancel(false);
				if(serverData['id']){
					if(!this.highlight){this.highlight = [];}
					this.highlight.push(serverData['id']);
					if(ccm_uploadedFiles && serverData['id']!='undefined') ccm_uploadedFiles.push(serverData['id']);
				}   
			} catch (ex) {
				this.debug(ex);
			}		
		},
		upload_complete_handler : uploadComplete, 
		queue_complete_handler : function(file){
			// queueComplete() from swfupload.handlers.js
			if (ccm_uploadedFiles.length > 0) {
				queueComplete();		
				ccm_filesUploadedDialog('<?php echo $searchInstance?>'); 
			}
		}
	});

	
});
</script>

<style type="text/css">

</style>

<form id="form1" action="<?php echo DISPATCHER_FILENAME?>" method="post" enctype="multipart/form-data">
		
		<table border="0" width="100%" cellspacing="0" cellpadding="0" id="ccm-file-upload-multiple-list">
		<tr>
			<th colspan="2"><div style="width: 80px; float: right"><span id="ccm-file-upload-multiple-spanButtonPlaceHolder"></span></div><?php echo t('Upload Queue');?></th>
		</tr>
		</table>
		
		<div class="ccm-spacer">&nbsp;</div><br/>
		
		<!--
		<div>

		<div id="ccm-file-upload-multiple-results-wrapper">

		<div style="width: 100px; float: right; text-align: right"></div>

		<div id="ccm-file-upload-multiple-results">0 <?php echo t('Files Uploaded');?></div>
		
		<div class="ccm-spacer">&nbsp;</div>
		
		</div>
		
		</div>
		<br style="clear:left;"/> //-->
		<div>
			<?php 
			
			print $h->button_js(t('Start Uploads'), 'swfu.startUpload()');
			print $h->button_js(t('Cancel All Uploads'), 'swfu.cancelQueue()', 'left', null,array('id'=>'ccm-file-upload-multiple-btnCancel', 'disabled' => 1));
			
			?>
		</div>

</form>

<div class="ccm-spacer">&nbsp;</div>
<br/>

<div class="ccm-note">
	<strong><?php echo t('Upload Max File Size: %s', ini_get('upload_max_filesize'))?></strong><br/>
	<strong><?php echo t('Post Max Size: %s', ini_get('post_max_size'))?></strong><br/>
</div>

</div>

<?php 
	$valt = Loader::helper('validation/token');
	$fh = Loader::helper('validation/file');
	Loader::library('file/types');
	
	$incoming_contents = $ch->getIncomingDirectoryContents();
?>
<div id="ccm-file-add-incoming-tab" style="display: none">
<h1><?php echo t('Add from Incoming Directory')?></h1>
<?php  if(!empty($incoming_contents)) { ?>
<form id="ccm-file-manager-multiple-incoming" method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
	<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
    <input type="hidden" name="ocID" value="<?php echo $ocID?>" />
		<table id="incoming_file_table" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="10%" valign="middle" class="center theader"><input type="checkbox" id="check_all_imports" name="check_all_imports" onclick="ccm_alSelectMultipleIncomingFiles(this);" value="" /></td>
				<td width="20%" valign="middle" class="center theader"></td>
				<td width="45%" valign="middle" class="theader"><?php echo t('Filename')?></td>
				<td width="25%" valign="middle" class="center theader"><?php echo t('Size')?></td>
			</tr>
		</table>
	<div id="incoming_files" class="incoming_file_importer">
		<table id="incoming_file_table" width="100%">
		<?php  foreach($incoming_contents as $filenum=>$file_array) { 
				$ft = FileTypeList::getType($file_array['name']);
		?>
			<tr>
				<td width="10%" valign="middle" class="center">
					<?php  if($fh->extension($file_array['name'])) { ?>
						<input type="checkbox" name="send_file<?php echo $filenum?>" class="ccm-file-select-incoming" value="<?php echo $file_array['name']?>" />
					<?php  } ?>
				</td>
				<td width="20%" valign="middle" class="center"><?php echo $ft->getThumbnail(1)?></td>
				<td width="45%" valign="middle"><?php echo $file_array['name']?></td>
				<td width="25%" valign="middle" class="center"><?php echo $file_array['size']?><?php echo t('Kb')?></div>
			</tr>
		<?php  } ?>
			<tr>
				<td style="text-align: center"><input type="checkbox" name="removeFilesAfterPost" value="1" /></td>
				<td colspan="2">
				
				<?php echo t('Remove files from incoming/ directory.')?></td>
				<td>
				<?php 
					print $form->submit('submit', t('Add Files'));
				?>
				</td>
			</tr>
		</table>
		<div class="clear"></div>
	</div>
	<?php echo $valt->output('import_incoming');?>
	<div style="clear: both">&nbsp;</div>

</form>
<?php  } else { ?>
	<?php echo t('No files found in %s', DIR_FILES_INCOMING)?>
<?php  } ?>
</div>

<div id="ccm-file-add-remote-tab" style="display: none">
<h1><?php echo t('Add From Remote URL')?></h1>
<form method="POST" id="ccm-file-manager-multiple-remote" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
	<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
    <input type="hidden" name="ocID" value="<?php echo $ocID?>" />
	<h3><?php echo t('Enter URL to valid file(s)')?></h3>
	<?php echo $valt->output('import_remote');?>

	<?php echo $form->text('url_upload_1', array('style' => 'width:90%'))?><br/>
	<?php echo $form->text('url_upload_2', array('style' => 'width:90%'))?><br/>
	<?php echo $form->text('url_upload_3', array('style' => 'width:90%'))?><br/>
	<?php echo $form->text('url_upload_4', array('style' => 'width:90%'))?><br/>
	<?php echo $form->text('url_upload_5', array('style' => 'width:90%'))?><br/>
	<br/>
	<?php 
		print $form->submit('submit', t('Add Files'));
	?>
</form>
</div>