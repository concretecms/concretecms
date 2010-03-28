<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

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
$searchInstance = $page . time();
$searchRequest = $pageList->getSearchRequest();
?>

<?php  if (!$sitemapCombinedMode) { ?>
	<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/ccm.sitemap.js"></script>
	<script type="text/javascript" src="<?php echo ASSETS_URL_CSS?>/ccm.sitemap.css"></script>
<?php  } ?>
<script type="text/javascript">$(function() {
	ccm_sitemapSetupSearch('<?php echo $searchInstance?>');
});
</script>

<div id="ccm-search-overlay" >
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('pages/search_form_advanced', array('searchInstance' => $searchInstance, 'sitemap_select_mode' => $sitemap_select_mode, 'searchDialog' => true, 'searchRequest' => $searchRequest)); ?>
				</td>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<div id="ccm-<?php echo $searchInstance?>-search-results">
						
							<?php  Loader::element('pages/search_results', array('searchInstance' => $searchInstance, 'sitemap_select_mode' => $sitemap_select_mode, 'searchDialog' => true, 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>