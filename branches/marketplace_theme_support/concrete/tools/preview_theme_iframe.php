<? 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<iframe id="previewInternalIFrame<?=time()?>" height="100%" style="width:100%; border:0px; " src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/preview_internal_theme.php?themeID=<?=intval($_REQUEST['themeID'])?>&previewCID=<?=intval($_REQUEST['previewCID'])?>&ctID=<?=intval($_REQUEST['ctID'])?>">
</iframe>