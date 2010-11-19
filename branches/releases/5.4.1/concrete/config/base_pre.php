<?php  defined('C5_EXECUTE') or die("Access Denied.");

if (!defined('DIR_BASE')) {
	define('DIR_BASE', dirname($_SERVER['SCRIPT_FILENAME']));
} 

if (!defined('DIR_CONFIG_SITE')) {
	define('DIR_CONFIG_SITE', DIR_BASE . '/config');
} 

# The core concrete directory. Either one per install or one per server
if (!defined('DIRNAME_APP')) {
	define('DIRNAME_APP', 'concrete');
}

if (!defined('DIRNAME_UPDATES')) {
	define('DIRNAME_UPDATES', 'updates');
}