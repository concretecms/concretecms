<? defined('C5_EXECUTE') or die(_("Access Denied."));

if (!defined('DIR_BASE')) {
	define('DIR_BASE', dirname($_SERVER['SCRIPT_FILENAME']));
}

# The core concrete directory. Either one per install or one per server
define('DIRNAME_APP', 'concrete');

define('DIRNAME_UPDATES', 'updates');