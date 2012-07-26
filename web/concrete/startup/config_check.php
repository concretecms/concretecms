<?
defined('C5_EXECUTE') or die("Access Denied.");
$config_check_failed = false;

if (version_compare(PHP_VERSION, '5.1.0', '<')) {
	die("Concrete5 requires PHP5.1.");
}

if (!defined('CONFIG_FILE')) { 
	define('CONFIG_FILE', DIR_CONFIG_SITE . '/site.php');
}

if (file_exists(CONFIG_FILE)) {
	include(CONFIG_FILE);
} else {
	// nothing is installed
	$config_check_failed = true;
}