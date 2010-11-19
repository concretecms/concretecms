<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$res = false;
if ($_REQUEST['uEmail'] && $_REQUEST['uHash']) {
	$res = UserInfo::validateUserEmailAddress($_REQUEST['uEmail'], $_REQUEST['uHash']);
}

if ($res) {

	header('Location: ' . View::url('/register', 'register_success'));
	exit;
	
}

?>