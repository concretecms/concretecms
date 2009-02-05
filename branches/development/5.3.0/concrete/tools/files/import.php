<ul class="ccm-dialog-tabs" id="ccm-file-import-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-upload-multiple"><?=t('Upload Multiple')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add Incoming')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
</ul>

<? $iframeNoCache = time(); ?>
<iframe src="" style="display: none" border="0" id="ccm-upload-more-options-frame<?=$iframeNoCache?>" name="ccm-upload-more-options-frame<?=$iframeNoCache?>"></iframe>

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
Form Here
</div>

<?php
	$valt = Loader::helper('validation/token');
	$ch = Loader::helper('concrete/file');
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
	<?php foreach($incoming_contents as $filenum=>$file_array) { ?>
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

