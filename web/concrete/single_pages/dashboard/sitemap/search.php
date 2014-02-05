<? defined('C5_EXECUTE') or die('Access Denied'); ?>
<?
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) { ?>
	
	<div class="ccm-dashboard-content-full" data-search="pages">
	<? Loader::element('pages/search', array('controller' => $searchController))?>
	</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=pages]').concreteAjaxSearch({
		result: <?=$result?>,
		onLoad: function(concreteSearch) {
			concreteSearch.subscribe('SearchBulkActionSelect', function(e, obj) {
				if (obj.option.val() == 'movecopy') {

				}
			});
		}
	});
});
</script>

<? } else { ?>
	<div class="ccm-pane-body">
		<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
	</div>
<? } ?>

