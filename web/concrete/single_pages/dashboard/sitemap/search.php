<? defined('C5_EXECUTE') or die('Access Denied'); ?>
<?
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) { ?>
	
	<div class="ccm-dashboard-content-full" data-search="pages">
	<? Loader::element('pages/search_form_advanced', array('columns' => $columns, 'searchRequest' => $searchRequest)); ?>
	<div data-search-results="pages">
		<? Loader::element('pages/search_results', array('columns' => $columns, 'results' => $results, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
	</div>
	</div>

<? } else { ?>
	<div class="ccm-pane-body">
		<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
	</div>
<? } ?>

