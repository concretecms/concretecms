<?php 

if ($config_check_failed) {
	$sp = preg_replace('/[^A-Za-z(\.)]/i', '', $_SERVER['PHP_SELF']);
	
	if ($sp == DIRNAME_APP . DISPATCHER_FILENAME_CORE) {
		die("You may not access this file directly.");
	}
}