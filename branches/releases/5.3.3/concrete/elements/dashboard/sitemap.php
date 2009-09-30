<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::helper('concrete/dashboard/sitemap');

if (isset($reveal)) {
	$nc = Page::getByID($reveal);
	$nh = Loader::helper('navigation');
	$cArray = $nh->getTrailToCollection($nc);
	foreach($cArray as $co) {
		ConcreteDashboardSitemapHelper::addOpenNode($co->getCollectionID());
	}
	ConcreteDashboardSitemapHelper::addOneTimeActiveNode($reveal);
}

?>
<div class="ccm-pane-controls">
<link href="<?php echo ASSETS_URL_CSS?>/ccm.sitemap.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
var CCM_SITEMAP_MODE = "<?php echo $sitemap_mode?>";
var CCM_BACK_TITLE = "<?php echo $previous_title?>";
var CCM_NODE_ACTION = "<?php echo $node_action?>";
var CCM_DIALOG_TITLE = "<?php echo $dialog_title?>";
var CCM_DIALOG_HEIGHT = "<?php echo $dialog_height?>";
var CCM_DIALOG_WIDTH = "<?php echo $dialog_width?>";
var CCM_TARGET_ID = "<?php echo $target_id?>";
</script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>

<h1 id="ccm-sitemap-title"><?php echo t('Sitemap')?></h1>


<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td style="width: 100%" valign="top">

<div id="tree">
	<ul id="tree-root0">
	</ul>
</div>

<?php  Loader::element('dashboard/sitemap_search_results') ?>


</td>
<td valign="top">

<?php  Loader::element('dashboard/sitemap_search') ?>

</td>
</tr>
</table>
