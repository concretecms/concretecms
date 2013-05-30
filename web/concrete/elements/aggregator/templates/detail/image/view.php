<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (is_array($image)) {
	$image = $image[0];
}
?>

<div class="ccm-aggregator-overlay">
	<img src="<?=$image->getPath()?>" style="max-width: 600px" />
	<div class="ccm-aggregator-thumbnail-caption"><?=$title?></div>
</div>
