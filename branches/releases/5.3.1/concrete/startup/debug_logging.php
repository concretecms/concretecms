<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$debug_level = Config::get('SITE_DEBUG_LEVEL');
switch($debug_level) {
	case DEBUG_DISPLAY_ERRORS:
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors', 1);
		break;
	default:
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors', 0);
		break;
}

if (ENABLE_LOG_DATABASE_QUERIES) {
	$db = Loader::db();
	$db->setLogging(true);
}