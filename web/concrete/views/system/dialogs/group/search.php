<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="groups" class="ccm-ui">
<? Loader::element('group/search', array('controller' => $searchController))?>
</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=groups]').concreteAjaxSearch({
		result: <?=$result?>,
		onLoad: function(concreteSearch) {
			concreteSearch.$element.on('click', 'a[data-group-id]', function() {
				ConcreteEvent.publish('GroupSearchDialogClick', {
					gID: $(this).attr('data-group-id'),
					gName: $(this).attr('data-group-name')
				});
				return false;
			})
		}
	});
});
</script>