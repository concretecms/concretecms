<?
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$cnt = Loader::controller('/dashboard/sitemap/search');
$pageList = $cnt->getRequestedSearchResults();
$columns = $cnt->get('columns');
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

ob_start();
Loader::element('pages/search_form_advanced', array('columns' => $columns, 'sitemap_select_callback' => $sitemap_select_callback, 'searchInstance' => $searchInstance, 'sitemap_select_mode' => $sitemap_select_mode, 'searchDialog' => true, 'searchRequest' => $searchRequest));
$searchForm = ob_get_contents();
ob_end_clean();

$v = View::getInstance();
$v->outputHeaderItems();

?>

<script type="text/javascript">$(function() {
	ccm_sitemapSetupSearch('<?=$searchInstance?>');
});
</script>

<div id="ccm-search-overlay" >
<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">
	<?=$searchForm?>
</div>

<? Loader::element('pages/search_results', array('columns' => $columns, 'searchInstance' => $searchInstance, 'sitemap_select_callback' => $sitemap_select_callback, 'sitemap_select_mode' => $sitemap_select_mode, 'searchDialog' => true, 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
</div>