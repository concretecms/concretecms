<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$config_check_failed = false;

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	die("Concrete5 requires PHP5.");
}

if (!defined("DIR_BASE")) {
	define('CONFIG_FILE', 'config/site.php');
} else {
	define('CONFIG_FILE', DIR_BASE . '/config/site.php');
}

if (!@include(CONFIG_FILE)) {
	// nothing is installed
	$config_check_failed = true;
}