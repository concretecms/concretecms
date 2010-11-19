<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$debug_level = Config::get('SITE_DEBUG_LEVEL');
switch($debug_level) {
	case DEBUG_DISPLAY_ERRORS:
		if(defined("E_DEPRECATED")) {
			error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED); // E_DEPRECATED required for php 5.3.0 because of depreciated function calls in 3rd party libs (adodb).
		} else {
			error_reporting(E_ALL ^ E_NOTICE);
		}
		ini_set('display_errors', 1);
		break;
	default:
		if(defined("E_DEPRECATED")) {
			error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
		} else {
			error_reporting(E_ALL ^ E_NOTICE);
		}
		ini_set('display_errors', 0);
		break;
}