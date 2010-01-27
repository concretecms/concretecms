<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$cnt = Loader::controller('/dashboard/sitemap/search');
$pageList = $cnt->getRequestedSearchResults();
$pages = $pageList->getPage();
$pagination = $pageList->getPagination();
$sitemap_mode = $_REQUEST['sitemap_mode'];
if (!$_REQUEST['sitemap_mode']) {
	$sitemap_mode = 'move_copy_delete';
}
$searchRequest = $pageList->getSearchRequest();
?>

<? if (!$sitemapCombinedMode) { ?>
	<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>
	<script type="text/javascript" src="<?=ASSETS_URL_CSS?>/ccm.sitemap.css"></script>
<? } ?>
<script type="text/javascript">$(function() {
	ccm_sitemapSetupSearch();
});
</script>

<div id="ccm-search-overlay" >
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<? Loader::element('pages/search_form_advanced', array('sitemap_mode' => $sitemap_mode, 'searchDialog' => true, 'searchRequest' => $searchRequest)); ?>
				</td>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<div id="ccm-page-search-results">
						
							<? Loader::element('pages/search_results', array('sitemap_mode' => $sitemap_mode, 'searchDialog' => true, 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>