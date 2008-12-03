<? 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<iframe id="previewExternalIFrame<?=time()?>" height="100%" style="width:100%; border:0px; " src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/preview_external_theme.php?random=<?=$_REQUEST['random']?>&themeCID=<?=intval($_REQUEST['themeCID'])?>&previewCID=<?=intval($_REQUEST['previewCID'])?>&themeHandle=<?=$_REQUEST['themeHandle']?>&ctID=<?=intval($_REQUEST['ctID'])?>">
</iframe>