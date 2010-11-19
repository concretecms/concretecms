<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$file = $controller->getFileObject();
	$w = $file->getAttribute('width');
	$h = $file->getAttribute('height');
	
$c = Page::getCurrentPage();
 
$vWidth=$w;
$vHeight=$h;
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?php echo $vWidth?>px; height:<?php echo $vHeight?>px;">
		<div style="padding:8px 0px; padding-top: <?php echo round($vHeight/2)-10?>px;"><?php echo t('Content disabled in edit mode.')?></div>
	</div>
<?php  }else{ ?>

	<div id="swfcontent<?php echo $bID?>">
	<?php echo t('You must install Adobe Flash to view this content.')?>
	</div>
	
	<script type="text/javascript">
	params = {
		bgcolor: "#000000",
		wmode:  "transparent",
		quality:  "<?php echo $controller->quality?>"
	};
	flashvars = {};
	swfobject.embedSWF("<?php echo $file->getRelativePath()?>", "swfcontent<?php echo $bID?>", "<?php echo $w?>", "<?php echo $h?>", "<?php echo $controller->minVersion?>", false, flashvars, params);
	</script>
	
<?php  } ?>