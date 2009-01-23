<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('search');
Loader::model('search/collection');
Loader::helper('concrete/dashboard/sitemap');

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

?>

<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.sitemap.css";</style>


<script type="text/javascript">
	var CCM_SITEMAP_MODE = 'full';
</script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>

<h1><span><?php echo t('Sitemap')?></span></h1>

<div class="ccm-dashboard-inner" >

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
	
	<div id="ccm-show-all-pages">
	<input type="checkbox" id="ccm-show-all-pages-cb" <?php  if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> checked <?php  } ?> />
	<label for="ccm-show-all-pages-cb"><?php echo t('Show System Pages')?></label>
	</div>
	
	</td>
	</tr>
	</table>
	
	</div>
</div>