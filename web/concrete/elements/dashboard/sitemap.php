<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::helper('concrete/dashboard/sitemap');


$cID = 1;

if (isset($reveal)) {
	$nc = Page::getByID($reveal);
	$cID = $nc->getCollectionID();
	$node = $nc->getCollectionParentID();
	if ($node < 1) {
		$node = 1;
	}
}

$cID = 1;
if (isset($selectedPageID)) {
	$cID = $selectedPageID;
}

?>
<div class="ccm-pane-controls">
<script type="text/javascript">
var CCM_BACK_TITLE = "<?=$previous_title?>";
var CCM_NODE_ACTION = "<?=$node_action?>";
var CCM_DIALOG_TITLE = "<?=$dialog_title?>";
var CCM_DIALOG_HEIGHT = "<?=$dialog_height?>";
var CCM_DIALOG_WIDTH = "<?=$dialog_width?>";
var CCM_TARGET_ID = "<?=$target_id?>";
var CCM_SITEMAP_EXPLORE_NODE = "<?=$node?>";
</script>

<? if (!$sitemapCombinedMode) { ?>
	<h1 id="ccm-sitemap-title"><?=t('Sitemap')?></h1>
<? } ?>

<div id="ccm-dashboard-select-display-mode">
<form>
<? 
$u = new User();
$sitemapOverlayPreference = $u->config('SITEMAP_OVERLAY_DISPLAY_MODE');
$display_mode = $sitemapOverlayPreference;

if (!isset($callback)) {
	$sitemap_select_callback = 'ccm_selectSitemapNode';
}
if ($sitemapOverlayPreference != 'explore') {
	$sitemapOverlayPreference = 'full';
	$display_mode = 'full';
	$node = 0;
} else if (!isset($node)) {
	$node = 1;
}
?>
<input type="radio" name="ccm-dashboard-display-mode" value="full" <? if ($sitemapOverlayPreference == 'full') { ?> checked <? } ?> onclick="ccm_sitemapSelectDisplayMode('<?=$instance_id?>', 'full', '<?=$select_mode?>', '<?=$cID?>')" /> <?=t('Full Sitemap')?>
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="ccm-dashboard-display-mode" value="explore" <? if ($sitemapOverlayPreference == 'explore') { ?> checked <? } ?> onclick="ccm_sitemapSelectDisplayMode('<?=$instance_id?>', 'explore', '<?=$select_mode?>', '<?=$cID?>')" /> <?=t('One Level Only')?>
</form>
</div>

<div id="tree" sitemap-wrapper="1" sitemap-select-callback="<?=$callback?>" sitemap-instance-id="<?=$instance_id?>" <? if ($display_mode == 'explore') { ?>class="ccm-sitemap-explore"<? } ?>>
	<ul id="tree-root0" tree-root-node-id="0" sitemap-select-callback="<?=$sitemap_select_callback?>" sitemap-display-mode="<?=$display_mode?>" sitemap-select-mode="<?=$select_mode?>" sitemap-instance-id="<?=$instance_id?>">
	</ul>
</div>

<script type="text/javascript">
$(function() {
	ccmSitemapLoad('<?=$instance_id?>', '<?=$display_mode?>', '<?=$select_mode?>', '<?=$node?>', '<?=$cID?>');
});
</script>

</div>