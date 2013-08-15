<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($config_check_failed) {
	define('ENABLE_LEGACY_CONTROLLER_URLS', true);
	// nothing is installed
	require($cdir . '/startup/session.php');
	$v = View::getInstance();
	$v->render('/install/');
	exit;
}