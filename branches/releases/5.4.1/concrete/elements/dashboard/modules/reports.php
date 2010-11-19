<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="dashboard-reports"><?php echo t('You must install Adobe Flash to view this content.')?></div>

<script type="text/javascript">
params = {
	'bgcolor': "#ffffff",
	'allowScriptAccess': "always",
	'wmode': "transparent"
}

flashvars = {
	"data":  "<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/chart_handler.php"
}

swfobject.embedSWF("<?php echo ASSETS_URL_FLASH?>/open_flash_chart.swf", "dashboard-reports", "330", "280", "9", false, flashvars, params);

</script>