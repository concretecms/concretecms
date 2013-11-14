<?
defined('C5_EXECUTE') or die("Access Denied.");

$tp = Loader::helper('concrete/user');
if (!$tp->canAccessUserSearchInterface()) { 
	die(t("You have no access to users."));
}

$cnt = new SearchUsersController();
$cnt->search();
$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
?>

<div data-search="users" class="ccm-ui">
<? Loader::element('users/search', array('controller' => $cnt))?>
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