<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?
$image = BASE_URL . $fv->getRelativePath();
$apiKey = 'be63e3b4ae4f0a0035caf17fc5f2f02b';
$url = 'http://www.picnik.com/service/';
$export = BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/files/edit?fID=' . $fv->getFileID();

?>

<iframe width="100%" height="100%" border="0" frameborder="0" id="<?=time()?>" src="<?=$url?>?_apikey=<?=$apiKey?>&_export=<?=rawurlencode($export)?>&_export_method=POST&export_agent=browser&_import=<?=rawurlencode($image)?>"></iframe>