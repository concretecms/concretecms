<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('search');
$sh = Loader::helper('concrete/dashboard/sitemap');

if (isset($_REQUEST['reveal'])) {
	$nc = new Collection($_REQUEST['reveal']);
	$nc = Page::getByID($_REQUEST['reveal']);
	$nh = Loader::helper('navigation');
	$cArray = $nh->getTrailToCollection($nc);
	foreach($cArray as $co) {
		ConcreteDashboardSitemapHelper::addOpenNode($co->getCollectionID());
	}
	ConcreteDashboardSitemapHelper::addOneTimeActiveNode($_REQUEST['reveal']);
}

$nodes = $sh->getSubNodes(0, 1, false, true);
$listHTML = $sh->outputRequestHTML('full', $nodes);

?>

<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.sitemap.css";</style>


<script type="text/javascript">
	var CCM_LAUNCHER_SITEMAP = 'full';
	$(function() {
		ccmSitemapLoad('full');
	});
</script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>

<h1><span><?=t('Sitemap')?></span></h1>

<div class="ccm-dashboard-inner" >

	<? if ($sh->canRead()) { ?>
	
		<div id="ccm-sitemap-message"></div>
	
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td style="width: 100%" valign="top">
		
		<div id="tree">
			<ul id="tree-root0" tree-root-node-id="0" sitemap-mode="full">
			<?=$listHTML?>
			</ul>
		</div>
	
		</td>
		<td valign="top">
		
		<div id="ccm-show-all-pages">
		<input type="checkbox" id="ccm-show-all-pages-cb" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> checked <? } ?> />
		<label for="ccm-show-all-pages-cb"><?=t('Show System Pages')?></label>
		</div>
		
		</td>
		</tr>
		</table>
		
		</div>
	
	<? } else { ?>
	
		<p><?=t("You do not have access to the sitemap.");?></p>
	
	<? } ?>
	
</div>