<?php defined('C5_EXECUTE') or die("Access Denied.");
if (strlen($relPathHover)) {
	$mouseOvers = ' onmouseover="this.src=\''.$relPathHover.'\'" onmouseout="this.src=\''.$relPath.'\'"';
}
if (strlen($linkURL)) { ?>
	<a href="<?=$linkURL?>"<?=strlen($hoverImage)?' class="ccm-image-hover"':''?>>
<?}?>
<img class="ccm-image-block primary img-responsive" alt="<?=$altText?>" src="<?=$relPath?>" <?=$sizeStr?>/>
<? if($relPathHover) { ?>
	<img class="ccm-image-block alternate img-responsive" alt="<?=$altText?>" src="<?=$relPathHover?>" <?=$sizeStr?>/>
<? }?>
<?if (strlen($linkURL)) { ?>
	</a>
<?}?>
