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
				ccm_event.publish('UserSearchDialogSelectUser', {
					uID: $(this).attr('data-user-id'),
					uEmail: $(this).attr('data-user-email'),
					uName: $(this).attr('data-user-name')
				});
				ccm_event.publish('UserSearchDialogAfterSelectUser');
				return false;
			});

			concreteSearch.subscribe('SearchBulkActionSelect', function(e) {
				if (e.eventData.value == 'choose_users') {
					$.each(e.eventData.items, function(i, item) {
						var $item = $(item);
						ccm_event.publish('UserSearchDialogSelectUser', {
							uID: $item.attr('data-user-id'),
							uEmail: $item.attr('data-user-email'),
							uName: $item.attr('data-user-name')
						});
					});
					ccm_event.publish('UserSearchDialogAfterSelectUser');
				}
			});
		}
	});
});
</script>