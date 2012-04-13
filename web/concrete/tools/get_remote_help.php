<?
defined('C5_EXECUTE') or die("Access Denied.");
session_write_close();

if ($_REQUEST['q']) {
	session_write_close();
	$r = Loader::helper("file")->getContents(MENU_HELP_SERVICE_URL . '?q=' . $_REQUEST['q']);
	if ($r) {
		print $r;
	} else {
		print Loader::helper('json')->encode(array());
	}	
	exit;
}