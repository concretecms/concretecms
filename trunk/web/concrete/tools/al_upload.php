<?

defined('C5_EXECUTE') or die(_("Access Denied.")); 
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

?>

<h1><?=t('Add Files')?></h1>

<div id="ccm-al-upload-complete" style="display: none"><?=t('Upload Complete!')?> <a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop(); ccm_alRefresh();"><?=t('Click here to close this window and refresh the library.')?></a></div>

<div id="ccm-al-flash-uploader"><?=t('You must install Adobe Flash to view this content.')?></div>

<script type="text/javascript">
params = {
	'bgcolor': "#fafafa"
}
flashvars = {
	"session": "<?=session_id()?>",
	"skin_url":  "<?=ASSETS_URL_FLASH?>/uploader/images/",
	"base_url":  "<?=BASE_URL?>",
	"upload_script":  "<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_upload_process_multiple.php?cID=<?=$_REQUEST['cID']?>",
	"callback_function":  "ccm_uploadComplete",
	"allow_file_types":  "all", // images | documents | all (all = images + documents)
	"allow_file_extensions":  "<?=UPLOAD_FILE_EXTENSIONS_ALLOWED?>"
}
swfobject.embedSWF("<?=ASSETS_URL_FLASH?>/uploader/uploader.swf", "ccm-al-flash-uploader", "565", "460", "8", false, flashvars, params);

ccm_uploadComplete = function() {
	$("#ccm-al-upload-complete").show('highlight');
	ccm_alRefresh();
}

</script>