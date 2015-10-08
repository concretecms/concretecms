<?php defined('C5_EXECUTE') or die('Access Denied'); ?>
<?
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) { ?>
	
	<div class="ccm-dashboard-content-full" data-search="pages">
	<?php Loader::element('pages/search', array('controller' => $searchController))?>
	</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=pages]').concretePageAjaxSearch({
		result: <?=$result?>
	});
});
</script>

<?php } else { ?>
	<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
<?php } ?>

