<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$file = $controller->getFileObject();
	$dimensions = $file->getDimensions();
	if (is_array($dimensions)) {
		$w = $dimensions[0];
		$h = $dimensions[1];
	} else {
		$w = 300;
		$h = 300;
	}
	
?>

<div id="swfcontent<?=$bID?>">
You must download <a href="http://www.adobe.com">Adobe Flash</a> to view this animation.
</div>

<script type="text/javascript">
params = {
	bgcolor: "#000000",
	wmode:  "transparent",
	quality:  "<?=$controller->quality?>"
};
flashvars = {};
swfobject.embedSWF("<?=REL_DIR_FILES_UPLOADED?>/<?=$file->getFilename()?>", "swfcontent<?=$bID?>", "<?=$w?>", "<?=$h?>", "<?=$controller->minVersion?>", false, flashvars, params);
</script>