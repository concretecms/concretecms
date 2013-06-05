<?
defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("You have no access to users."));
}

$u = new User();
$cnt = Loader::controller('/dashboard/users/search');
$userList = $cnt->getRequestedSearchResults();
$columns = $cnt->get('columns');

$users = $userList->getPage();
$pagination = $userList->getPagination();

$searchType = Loader::helper('text')->entities($_REQUEST['searchType']);


Loader::element('users/search_results', array('columns' => $columns, 'users' => $users, 'userList' => $userList, 'searchType' => $searchType, 'pagination' => $pagination));
