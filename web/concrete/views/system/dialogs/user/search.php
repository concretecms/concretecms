<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="users" class="ccm-ui">
<? Loader::element('users/search', array('controller' => $searchController))?>
</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=users]').concreteAjaxSearch({
		result: <?=$result?>,
		onLoad: function(concreteSearch) {
			concreteSearch.$element.on('click', 'a[data-user-id]', function() {
				ccm_event.publish('UserSearchDialogClick', {
					uID: $(this).attr('data-user-id'),
					uEmail: $(this).attr('data-user-email'),
					uName: $(this).attr('data-user-name')
				});
				return false;
			})
		}
	});
});
</script>