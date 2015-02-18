<?php
defined('C5_EXECUTE') or die("Access Denied.");
session_write_close();

if ($_REQUEST['q']) {
	session_write_close();
    $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.menu_help_service');
	$r = Loader::helper("file")->getContents($url . '?q=' . $_REQUEST['q']);
	if ($r) {
		print $r;
	} else {
		print Loader::helper('json')->encode(array());
	}
	exit;
}
