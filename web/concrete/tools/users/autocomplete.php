<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
if ($valt->validate('quick_user_select_' . $_REQUEST['key'], $_REQUEST['token'])) {
	$u = new User();
	Loader::model('user_list');
	$db = Loader::db();
	$userList = new UserList();
	if ($_GET['term'] != '') {
		$term = $db->quote($_GET['term'].'%');
		$userList->filter(false, '( u.uName LIKE ' . $term . ')');
	}
	$userList->sortBy('uName','ASC');
	$users = $userList->get(7);
	$userNames = array();
	foreach($users as $ui) {
		$userNames[] = $ui->getUserName();
	}
	$jh = Loader::helper('json');
	echo $jh->encode($userNames);
}
