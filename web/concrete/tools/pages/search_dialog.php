<?
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$cnt = Loader::controller('/dashboard/sitemap/search');
$pageList = $cnt->getRequestedSearchResults();
$pages = $pageList->getPage();
$pagination = $pageList->getPagination();
if (!isset($sitemap_select_mode)) {
	$sitemap_select_mode = $_REQUEST['sitemap_select_mode'];
	if (!$_REQUEST['sitemap_select_mode']) {
		$sitemap_select_mode = 'move_copy_delete';
	}
}
$sitemap_select_callback = $_REQUEST['callback'];
if (!$_REQUEST['callback']) {
	$sitemap_select_callback = 'ccm_selectSitemapNode';
}
$searchInstance = $page . time();
$searchRequest = $pageList->getSearchRequest();
?>

<script type="text/javascript">$(function() {
	ccm_sitemapSetupSearch('<?=$searchInstance?>');
});
</script>

<div id="ccm-search-overlay" >
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<? Loader::element('pages/search_form_advanced', array('sitemap_select_callback' => $sitemap_select_callback, 'searchInstance' => $searchInstance, 'sitemap_select_mode' => $sitemap_select_mode, 'searchDialog' => true, 'searchRequest' => $searchRequest)); ?>
				</td>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<div id="ccm-<?=$searchInstance?>-search-results">
						
							<? Loader::element('pages/search_results', array('searchInstance' => $searchInstance, 'sitemap_select_callback' => $sitemap_select_callback, 'sitemap_select_mode' => $sitemap_select_mode, 'searchDialog' => true, 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>