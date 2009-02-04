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

<div id="ccm-file-add-incoming-tab" style="display: none">
<h1>Add Files from Incoming Directory</h1>
Form Here
</div>

<div id="ccm-file-add-remote-tab" style="display: none">
<h1>Add Remote Files</h1>
Form Here
</div>

