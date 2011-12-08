<? defined('C5_EXECUTE') or die('Access Denied'); ?>
<script type="text/javascript">
CCM_LAUNCHER_SITEMAP = 'search'; // we need this for when we are moving and copying
CCM_SEARCH_INSTANCE_ID = '<?=$searchInstance?>';
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Search Pages'), t('Search the pages of your site and perform bulk actions on them.'), false, false);?>

	<?
	$dh = Loader::helper('concrete/dashboard/sitemap');
	if ($dh->canRead()) { ?>
	
		<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">
			<? Loader::element('pages/search_form_advanced', array('columns' => $columns, 'searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
		</div>
	
		<? Loader::element('pages/search_results', array('columns' => $columns, 'searchInstance' => $searchInstance, 'searchType' => 'DASHBOARD', 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
	
	<? } else { ?>
		<div class="ccm-pane-body">
			<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
		</div>	
	
	<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>