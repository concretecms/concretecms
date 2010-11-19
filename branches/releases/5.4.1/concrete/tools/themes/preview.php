<?php  
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($_REQUEST['themeID'])) {
	// internal theme
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/themes/preview_internal?random=' . time() . '&themeID=' . intval($_REQUEST['themeID']) . '&previewCID=' . intval($_REQUEST['previewCID']) . '&ctID=' . intval($_REQUEST['ctID']);
} else {
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/themes/preview_external?random=' . time() . '&themeCID=' . intval($_REQUEST['themeCID']) . '&previewCID=' . intval($_REQUEST['previewCID']) . '&themeHandle=' . $_REQUEST['themeHandle'] . '&ctID=' . intval($_REQUEST['ctID']);
}
?>
<iframe id="previewTheme<?php echo time()?>" height="100%" style="width:100%; border:0px; " src="<?php echo $url?>"></iframe>