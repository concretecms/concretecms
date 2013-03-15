<?
defined('C5_EXECUTE') or die("Access Denied.");
//echo($controller->getContentAndGenerate());
if (strlen($relPathHover)) {
	$mouseOvers = ' onmouseover="this.src=\''.$relPathHover.'\'" onmouseout="this.src=\''.$relPath.'\'"';
}
if (strlen($linkURL)) {
	$linkClass = $hoverImage?'"ccm-image-hover"':'';
?>
	<a href="<?=$linkURL?>"<?=strlen($linkClass)?' class='.$linkClass:''?>>
<?}?>
<style>
	img.ccm-image-block.alternate {
		display:none;
	}
</style>
<script>
$(function(){
$( 'img.ccm-image-block.primary' ).hover(function(){
		$(this).attr('oldsrc',$(this).attr('src'));
		$(this).attr('src',$(this).next('img.ccm-image-block.alternate').attr('src'));
	},
	function(){
		$(this).attr('src',$(this).attr('oldsrc'));
	});
});
</script>

<img class="ccm-image-block primary" alt="<?=$altText?>" src="<?=$relPath?>" <?=$sizeStr?>/>
<img class="ccm-image-block alternate" alt="<?=$altText?>" src="<?=$relPathHover?>" <?=$sizeStr?>/>
<?if (strlen($linkURL)) { ?>
	</a>
<?}?>
