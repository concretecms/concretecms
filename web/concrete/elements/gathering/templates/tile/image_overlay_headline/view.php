<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (is_array($image)) {
	$image = $image[0];
}

?>
<div class="ccm-gathering-image-overlay-headline-byline">
		<img src="<?=$image->getPath()?>" alt="<?php echo t('Preview Image') ?>" />
	<div class="ccm-gathering-tile-image-overlay-headline-byline-description">
		<p class="overlay-title"><?=$title; ?></p>
		<p class="overlay-byline"><?= t('by ') . $author ?></p>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>