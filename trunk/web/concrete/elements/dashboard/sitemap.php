<?
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
<link href="<?=ASSETS_URL_CSS?>/ccm.sitemap.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
var CCM_SITEMAP_MODE = "<?=$sitemap_mode?>";
var CCM_BACK_TITLE = "<?=$previous_title?>";
var CCM_NODE_ACTION = "<?=node_action?>";
</script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>

<h1 id="ccm-sitemap-title"><?=t('Sitemap')?></h1>


<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td style="width: 100%" valign="top">

<div id="tree">
	<ul id="tree-root0">
	</ul>
</div>

<?// Loader::element('dashboard/sitemap_search_results') ?>


</td>
<td valign="top">

<?// Loader::element('dashboard/sitemap_search') ?>

</td>
</tr>
</table>