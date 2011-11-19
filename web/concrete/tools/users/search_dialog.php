<?
defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("You have no access to users."));
}

$cnt = Loader::controller('/dashboard/users/search');
$userList = $cnt->getRequestedSearchResults();
$users = $userList->getPage();
$pagination = $userList->getPagination();
$columns = $cnt->get('columns');

if (!isset($mode)) {
	$mode = $_REQUEST['mode'];
}
?>

<div class="ccm-ui">
<div id="ccm-search-overlay" >
<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">
	<? Loader::element('users/search_form_advanced', array('columns' => $columns, 'mode' => $mode)) ; ?>
</div>

<? Loader::element('users/search_results', array('columns' => $columns, 'mode' => $mode, 'users' => $users, 'userList' => $userList, 'pagination' => $pagination)); ?>
</div>
</div>

<script type="text/javascript">
$(function() {
	ccm_setupAdvancedSearch('user');
});
</script>