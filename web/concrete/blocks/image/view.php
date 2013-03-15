<?
defined('C5_EXECUTE') or die("Access Denied.");
//echo($controller->getContentAndGenerate());
if (strlen($relPathHover)) {
	$mouseOvers = ' onmouseover="this.src=\''.$relPathHover.'\'" onmouseout="this.src=\''.$relPath.'\'"';
if (strlen($linkURL)) {?>
	$linkClass = $hoverImage?'"ccm-image-hover"':'';
<?}?>
	<a href="<?=$linkURL?>"<?=strlen($linkClass)?' class='.$linkClass:''?>>
<?}?>
<img class="ccm-image-block" alt="<?=$altText?>" src="<?=$relPath?>" <?=$sizeStr?><?=$mouseOvers?>/>
<?if (strlen($linkURL)) { ?>
	</a>
<?}?>
