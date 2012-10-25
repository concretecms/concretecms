<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ch = Loader::helper('concrete/file');
$h = Loader::helper('concrete/interface');
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
<ul class="tabs" id="ccm-file-import-tabs">
<li class="active"><a href="javascript:void(0)" id="ccm-file-add-multiple"><?=t('Upload Multiple')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add Incoming')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
</ul>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.handlers.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.fileprogress.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/swfupload/swfupload.queue.js"></script>

<script type="text/javascript">
var ccm_fiActiveTab = "ccm-file-add-multiple";
$("#ccm-file-import-tabs a").click(function() {

	$("li.active").removeClass('active');
	var activesection = ccm_fiActiveTab.substring(13);
	var wind = $(this).parentsUntil('.ui-dialog').parent();
	var bp = wind.find('.ui-dialog-buttonpane');
	$("#dialog-buttons-" + activesection).html(bp.html());

	$("#" + ccm_fiActiveTab + "-tab").hide();
	ccm_fiActiveTab = $(this).attr('id');
	if (ccm_fiActiveTab != 'ccm-file-add-multiple') {
		$('#ccm-file-add-multiple-outer').css('visibility', 'hidden');
	} else {
		$('#ccm-file-add-multiple-outer').css('visibility', 'visible');
	}

	$(this).parent().addClass("active");
	$("#" + ccm_fiActiveTab + "-tab").show();
	var section = $(this).attr('id').substring(13);

	var buttons = $("#dialog-buttons-" + section);
	bp.html(buttons.html());

});
</script>

<div style="position: absolute; top: 107px; right: 15px;" id="ccm-file-add-multiple-outer"><span id="ccm-file-add-multiple-spanButtonPlaceHolder"></span></div>

<div id="ccm-file-add-multiple-tab">
	<div style="float: right">
		<div class="help-block" style="margin-top: 11px">
		<?=t('Upload Max: %s.', ini_get('upload_max_filesize'))?>
		<?=t('Post Max: %s', ini_get('post_max_size'))?>
		</div>
	</div>

<h3><?php echo t('Upload Multiple Files')?></h3>

<?
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

		flash_url : "<?=ASSETS_URL_FLASH?>/swfupload/swfupload.swf",
		upload_url : "<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/multiple",
		post_params: {'ccm-session' : "<?php echo session_id(); ?>",'searchInstance': '<?=$searchInstance?>', 'ocID' : '<?=$ocID?>', 'ccm_token' : '<?=$valt->generate("upload")?>'},
		file_size_limit : "<?=$umf?>",
		/* file_types : "<?=$types?>", */
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		file_types_description : "<?php echo t('All Files') ?>",
		file_upload_limit : 100,
		button_cursor: SWFUpload.CURSOR.HAND,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "ccm-file-add-multiple-list",
			cancelButtonId : "ccm-file-add-multiple-btnCancel"
		},
		debug: false,

		// Button settings
		button_image_url: "<?=ASSETS_URL_IMAGES?>/icons/add_file_swfupload.png",	// Relative to the Flash file
		button_width: "80",
		button_text: '<span class="uploadButtonText"><?=t('Add Files')?><\/span>',
		button_height: "16",
		button_text_left_padding: 18,
		button_text_style: ".uploadButtonText {background-color: #eee; font-family: Helvetica Neue, Helvetica, Arial}",
		button_placeholder_id: "ccm-file-add-multiple-spanButtonPlaceHolder",
		
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
				
				progress.setStatus("<?php echo t('Uploading...')?> ("+percent+"%)");
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
				jQuery.fn.dialog.closeTop();
				setTimeout(function() { 
					ccm_filesUploadedDialog('<?=$searchInstance?>'); 
				}, 100);
			}
		}
	});

	
});
</script>

<style type="text/css">

</style>

<form id="form1" action="<?=DISPATCHER_FILENAME?>" method="post" enctype="multipart/form-data">

	
		<table border="0" width="100%" cellspacing="0" cellpadding="0" id="ccm-file-add-multiple-list" class="table table-striped">
		<tr>
			<th colspan="2"><?=t('Upload Queue');?></th>
		</tr>
		</table>
		
		<div class="ccm-spacer">&nbsp;</div><br/>
		
		<!--
		<div>

		<div id="ccm-file-add-multiple-results-wrapper">

		<div style="width: 100px; float: right; text-align: right"></div>

		<div id="ccm-file-add-multiple-results">0 <?=t('Files Uploaded');?></div>
		
		<div class="ccm-spacer">&nbsp;</div>
		
		</div>
		
		</div>
		<br style="clear:left;"/> //-->
		<div class="dialog-buttons">
			<?
			
			print $h->button_js(t('Start Uploads'), 'swfu.startUpload()', 'right', 'primary');
			print $h->button_js(t('Cancel'), 'swfu.cancelQueue()', 'left', null,array('id'=>'ccm-file-add-multiple-btnCancel', 'disabled' => 1));
			
			?>
		</div>
		
		<? // don't ask why we have to this. it's because we're swapping out buttons with the tabs. Ugh. ?>
		<div style="display: none" id="dialog-buttons-multiple">
			<?
			
			print $h->button_js(t('Start Uploads'), 'swfu.startUpload()', 'right', 'primary');
			print $h->button_js(t('Cancel'), 'swfu.cancelQueue()', 'left', null,array('id'=>'ccm-file-add-multiple-btnCancel', 'disabled' => 1));
			
			?>
		</div>
		
		<div style="display: none" id="dialog-buttons-incoming">
			<?
				print $form->submit('submit', t('Import Files'), array('onclick' => "jQuery.fn.dialog.showLoader();$('#ccm-file-manager-multiple-incoming').submit()", 'class' => 'primary ccm-button-right'));
			?>
		</div>
		
		<div id="dialog-buttons-remote" style="display: none">
			<?
				print $form->submit('submit', t('Import Files'), array('onclick' => "jQuery.fn.dialog.showLoader();$('#ccm-file-manager-multiple-remote').submit()", 'class' => 'primary ccm-button-right'));
			?>
		</div>

</form>

<div class="ccm-spacer">&nbsp;</div>
<br/>

</div>

<?php
	$valt = Loader::helper('validation/token');
	$fh = Loader::helper('validation/file');
	Loader::library('file/types');
	
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
				<th width="10%" valign="middle" class="center theader"><input type="checkbox" id="check_all_imports" name="check_all_imports" onclick="ccm_alSelectMultipleIncomingFiles(this);" value="" /></td>
				<th width="20%" valign="middle" class="center theader"></td>
				<th width="45%" valign="middle" class="theader"><?=t('Filename')?></td>
				<th width="25%" valign="middle" class="center theader"><?=t('Size')?></td>
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
				<td width="25%" valign="middle" class="center"><?=$file_array['size']?><?=t('Kb')?></div>
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