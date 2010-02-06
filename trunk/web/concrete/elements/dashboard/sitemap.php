<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::helper('concrete/dashboard/sitemap');

/*
$cID = 1;

if (isset($reveal)) {
	$nc = Page::getByID($reveal);
	$cID = $nc->getCollectionID();
	$node = $nc->getCollectionParentID();
	if ($node < 1) {
		$node = 1;
	}
}
*/

$cID = 1;
if (isset($selectedPageID)) {
	$cID = $selectedPageID;
}

if (!isset($node)) {
	$node = 1;
}
?>
<div class="ccm-pane-controls">

<? if (!$sitemapCombinedMode) { ?>
<link href="<?=ASSETS_URL_CSS?>/ccm.sitemap.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>
<? } ?>

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

<div id="tree" class="ccm-sitemap<? if ($display_mode == 'explore') { ?>-explore<? } ?>">
	<ul id="tree-root0" tree-root-node-id="0" sitemap-display-mode="<?=$display_mode?>" sitemap-select-mode="<?=$select_mode?>" sitemap-instance-id="<?=$instance_id?>">
	</ul>
</div>

<script type="text/javascript">
$(function() {
	ccmSitemapLoad('<?=$instance_id?>', '<?=$display_mode?>', '<?=$select_mode?>', '<?=$node?>', '<?=$cID?>');
});
</script>

</div>