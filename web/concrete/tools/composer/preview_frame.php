<?defined('C5_EXECUTE') or die(_("Access Denied."));

$url = REL_DIR_FILES_TOOLS_REQUIRED."/composer/preview?previewCID=".$_REQUEST['previewCID'];
?>
<iframe id="previewComposerDraft<?=time()?>" height="100%" style="width:100%; border:0px; " src="<?=$url?>"></iframe>

