<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$file = $controller->getFileObject();
	$w = $file->getAttribute('width');
	$h = $file->getAttribute('height');
	
$c = Page::getCurrentPage();
 
$vWidth=$w;
$vHeight=$h;
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?=$vWidth?>px; height:<?=$vHeight?>px;">
		<div style="padding:8px 0px; padding-top: <?=round($vHeight/2)-10?>px;"><?=t('Content disabled in edit mode.')?></div>
	</div>
<? }else{ ?>

	<div id="swfcontent<?=$bID?>">
	<?=t('You must install Adobe Flash to view this content.')?>
	</div>
	
	<script type="text/javascript">
	params = {
		bgcolor: "#000000",
		wmode:  "transparent",
		quality:  "<?=$controller->quality?>"
	};
	flashvars = {};
	swfobject.embedSWF("<?=$file->getRelativePath()?>", "swfcontent<?=$bID?>", "<?=$w?>", "<?=$h?>", "<?=$controller->minVersion?>", false, flashvars, params);
	</script>
	
<? } ?>