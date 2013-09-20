<? 
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($_REQUEST['themeID'])) {
	// internal theme
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/themes/preview_internal?random=' . time() . '&themeID=' . intval($_REQUEST['themeID']) . '&previewCID=' . intval($_REQUEST['previewCID']) . '&ptID=' . intval($_REQUEST['ptID']);
} else {
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/themes/preview_external?random=' . time() . '&themeCID=' . intval($_REQUEST['themeCID']) . '&previewCID=' . intval($_REQUEST['previewCID']) . '&themeHandle=' . $_REQUEST['themeHandle'] . '&ptID=' . intval($_REQUEST['ptID']);
}
?>
<iframe id="previewTheme<?=time()?>" height="100%" style="width:100%; border:0px; " src="<?=$url?>"></iframe>