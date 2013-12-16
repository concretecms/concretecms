<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="pages" class="ccm-ui">
<? Loader::element('pages/search', array('controller' => $searchController))?>
</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=pages]').concreteAjaxSearch({
		result: <?=$result?>,
		onUpdateResults: function(concreteSearch) {
			var $e = concreteSearch.$element;
			// hide the checkbox since they're pointless here.
			$e.find('.ccm-search-results-checkbox').parent().remove();
			// hide the bulk item selector.
			$e.find('select[data-bulk-action]').parent().remove();

			$e.unbind('.concretePageSearchHoverPage');
			$e.on('mouseover.concretePageSearchHoverPage', 'tr[data-launch-search-menu]', function() {
				$(this).addClass('ccm-search-select-hover');
			});
			$e.on('mouseout.concretePageSearchHoverPage', 'tr[data-launch-search-menu]', function() {
				$(this).removeClass('ccm-search-select-hover');
			});
			$e.unbind('.concretePageSearchChoosePage').on('click.concretePageSearchChoosePage', 'tr[data-launch-search-menu]', function() {
				ConcreteEvent.publish('SitemapSelectPage', {cID: $(this).attr('data-page-id'), title: $(this).attr('data-page-name')});
				return false;
			});

		}
	});
});
</script>

<style type="text/css">
	div[data-search=pages].ccm-ui form.ccm-search-fields {
		margin-left: 0px;
	}
</style>