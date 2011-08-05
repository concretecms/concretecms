<?

defined('C5_EXECUTE') or die("Access Denied.");

if ($config_check_failed) {
	if (basename($_SERVER['PHP_SELF']) == DISPATCHER_FILENAME_CORE) {
		die("Access Denied.");
	}
}