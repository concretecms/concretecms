<?php 

if ($config_check_failed) {
	// nothing is installed
	$v = View::getInstance();
	$v->render('/install/');
	exit;
}