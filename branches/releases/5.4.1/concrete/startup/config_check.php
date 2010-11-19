<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$config_check_failed = false;

if (version_compare(PHP_VERSION, '5.1.0', '<')) {
	die("Concrete5 requires PHP5.1.");
}

if (!defined('CONFIG_FILE')) { 
	if (!defined("DIR_BASE")) {
		define('CONFIG_FILE', DIR_CONFIG_SITE . '/site.php');
	} else {
		define('CONFIG_FILE', DIR_CONFIG_SITE . '/site.php');
	}
}  

if (!@include(CONFIG_FILE)) {
	// nothing is installed
	$config_check_failed = true;
}