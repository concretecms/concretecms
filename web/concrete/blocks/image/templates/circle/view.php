<?php defined('C5_EXECUTE') or die("Access Denied.");
if (strlen($linkURL)) { ?>
	<a href="<?=$linkURL?>"<?=strlen($hoverImage)?' class="ccm-image-hover"':''?>>
<? } ?>

<img class="ccm-image-block primary img-responsive img-circle" alt="<?=$altText?>" src="<?=$relPath?>" <?=$sizeStr?>/>

<?if (strlen($linkURL)) { ?>
	</a>
<?}?>
