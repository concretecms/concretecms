<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
if ($config_check_failed) {
	// nothing is installed
	$v = View::getInstance();
	$v->render('/install/');
	exit;
}