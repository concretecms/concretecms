<?php 
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

<div id="swfcontent<?php echo $bID?>">
You must download <a href="http://www.adobe.com">Adobe Flash</a> to view this animation.
</div>

<script type="text/javascript">
params = {
	bgcolor: "#000000",
	wmode:  "transparent",
	quality:  "<?php echo $controller->quality?>"
};
flashvars = {};
swfobject.embedSWF("<?php echo REL_DIR_FILES_UPLOADED?>/<?php echo $file->getFilename()?>", "swfcontent<?php echo $bID?>", "<?php echo $w?>", "<?php echo $h?>", "<?php echo $controller->minVersion?>", false, flashvars, params);
</script>