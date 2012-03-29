<?
defined('C5_EXECUTE') or die("Access Denied.");
$debug_level = Config::get('SITE_DEBUG_LEVEL');
switch($debug_level) {
	case DEBUG_DISPLAY_ERRORS:
		ini_set('display_errors', 1);
		break;
	default:
		ini_set('display_errors', 0);
		break;
}