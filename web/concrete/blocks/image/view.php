<?
defined('C5_EXECUTE') or die("Access Denied.");
//echo($controller->getContentAndGenerate());
?>
<?if (strlen($linkURL)) {
	$linkClass = $hoverImage?'"ccm-image-hover"':'';
?>
	<a href="<?=$linkURL?>"<?=strlen($linkClass)?' class='.$linkClass:''?>>
<?}?>
	<img class="ccm-image-block" alt="<?=$altText?>" src="<?=$relPath?>" {$sizeStr}  />
<?if (strlen($linkURL)) { ?>
	</a>
<?}?>
