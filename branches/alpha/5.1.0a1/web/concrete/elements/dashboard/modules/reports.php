<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="dashboard-reports">You must install Adobe Flash to view this report.</div>

<script type="text/javascript">
params = {
	'bgcolor': "#ffffff",
	'allowScriptAccess': "always"
}

flashvars = {
	"data":  "<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/chart_handler.php"
}

swfobject.embedSWF("<?=ASSETS_URL_FLASH?>/open_flash_chart.swf", "dashboard-reports", "330", "280", "9", false, flashvars, params);

</script>