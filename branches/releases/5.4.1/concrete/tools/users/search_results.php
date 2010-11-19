<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(_("You have no access to users."));
}

$u = new User();
$cnt = Loader::controller('/dashboard/users/search');
$userList = $cnt->getRequestedSearchResults();

$users = $userList->getPage();
$pagination = $userList->getPagination();


Loader::element('users/search_results', array('users' => $users, 'userList' => $userList, 'pagination' => $pagination));
