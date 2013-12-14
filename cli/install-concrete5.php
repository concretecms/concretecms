#!/usr/bin/env php
<?php
$helpText = <<<EOT
    Usage: install-concrete5.php [OPTION]...
    install concrete5 from the shell

        --db-server=<hostname>             Location of database server
        --db-username=<username>           Database username
        --db-password=<password>           Database password
        --db-database=<name>               Database name
        --admin-email=<email>              Email of the admin user of the install
        --admin-password=<password>        Password of the admin user of the install
        --starting-point=<starting point>  Starting point to use
        --target=<target location>         Target location of the install
        --site=<site name>                 Name of the site
        --core=<core location>             Location of the core concrete5 files
        --reinstall=<no/yes>               If already installed at the target location
                                           Delete current install and reinstall
        --demo-username=<username>         Additional user username
        --demo-password=<password>         Additional user password
        --demo-email=<email>               Additional user email
        --config=<file>                    Use configuration file for installation

    Requires minimum version 5.5.1 of concrete5
    Report bugs to <https://github.com/concrete5/concrete5-cli>
    For use with the concrete5 CMS <http://www.concrete5.org>

EOT;
if (count($argv) === 1) {
	echo $helpText;
	exit;
}

define('FILE_PERMISSIONS_MODE', 0777);
define('DIRECTORY_PERMISSIONS_MODE', 0777);
define('APP_VERSION_CLI_MINIMUM', '5.5.1');
define('BASE_URL', 'http://localhost');

error_reporting(0);
ini_set('display_errors', 0);
define('C5_EXECUTE', true);

$defaults = array(
	'db-server' => '',
	'db-username' => '',
	'db-password' => '',
	'db-database' => '',
	'admin-email' => 'admin@example.com',
	'admin-password' => '',
	'starting-point' => 'blank',
	'target' => '',
	'site' => 'concrete5 Site',
	'core' => '',
	'reinstall' => 'no',
	'demo-username' => 'demo',
	'demo-password' => '12345',
	'demo-email' => 'demo@example.com'
);

$args = array();
foreach (array_slice($argv, 1) as $arg) {
	$opt = explode('=', $arg);
	$args[str_replace('--', '', $opt[0])] = trim(isset($opt[1]) ? $opt[1] : '', '\'"');
}
if (array_key_exists('help', $args)) {
	echo $helpText;
	exit;
}

$config = array();
if (array_key_exists('config', $args)) {
	if (!file_exists($args['config'])) {
		die("ERROR: Config file not found.\n");
	}
	include($args['config']);
}
$config = array_merge($defaults, $config, $args);

if ($config['target']) {
	$target = $config['target'];
	if (substr($target, 0, 1) !== '/') {
		$target = dirname(__FILE__) . '/' . $target;
	}
	if (!file_exists($target)) {
		die("ERROR: Target location not found.\n");
	}
	define('DIR_BASE', $target);
} else {
	define('DIR_BASE', dirname(__FILE__));
}

if ($config['core']) {
	if (substr($config['core'], 0, 1) == '/') {
		$corePath = $config['core'];
	} else {
		$corePath = dirname(__FILE__) . '/' . $config['core'];
	}
} elseif (file_exists(dirname(__FILE__) . '/' . 'install-concrete5-conf.php')) {
		$corePath = dirname(__FILE__) . '/' . 'install-concrete5-conf.php';
} else {
	$corePath = DIR_BASE . '/concrete';
}
if (!file_exists($corePath . '/config/version.php')) {
	die("ERROR: Invalid concrete5 core.\n");
} else {
	include($corePath . '/config/version.php');
}

if ($config['reinstall'] === 'yes' && is_file(DIR_BASE . '/config/site.php')) {
	unlink(DIR_BASE . '/config/site.php');
}
if (file_exists(DIR_BASE . '/config/site.php')) {
	die("ERROR: concrete5 is already installed.\n");
}		

## Startup check ##	
require($corePath . '/config/base_pre.php');

## Load the base config file ##
require($corePath . '/config/base.php');

## Required Loading
require($corePath . '/startup/required.php');

## Setup timezone support
require($corePath . '/startup/timezone.php'); // must be included before any date related functions are called (php 5.3 +)

## First we ensure that dispatcher is not being called directly
require($corePath . '/startup/file_access_check.php');

require($corePath . '/startup/localization.php');

## Security helpers
require($corePath . '/startup/security.php');

## Autoload core classes
spl_autoload_register(array('Loader', 'autoloadCore'), true);

## Load the database ##
Loader::database();

require($corePath . '/startup/autoload.php');

## Exception handler
require($corePath . '/startup/exceptions.php');

## Set default permissions for new files and directories ##
require($corePath . '/startup/file_permission_config.php');

## Startup check, install ##	
require($corePath . '/startup/magic_quotes_gpc_check.php');

## Default routes for various content items ##
require($corePath . '/config/theme_paths.php');

## Load session handlers
require($corePath . '/startup/session.php');
if ($config['reinstall'] === 'yes') {
	require($corePath . '/config/app.php');

	// Remove all files from the files directory
	function removeDemoFiles($path) {
		$path .= end(str_split($path)) !== '/' ? '/' : '';
		foreach (glob($path . "*") as $file) {
			if (is_dir($file)) removeDemoFiles($file);
			if (is_file($file)) unlink($file);
		}
		// Remove Directory once Files have been removed (If Exists)
		if (is_dir($path) && $path !== DIR_FILES_UPLOADED . '/') rmdir($path); 
	}
	removeDemoFiles(DIR_FILES_UPLOADED . '/');


	foreach (Loader::db($config['db-server'], $config['db-username'], $config['db-password'], $config['db-database'])->MetaTables('TABLES') as $table) {
		Loader::db($config['db-server'], $config['db-username'], $config['db-password'], $config['db-database'])->Execute('DROP TABLE '.$table);
	}
	Loader::library('cache');
	Cache::flush();
}

## Startup check ##	
require($corePath . '/startup/encoding_check.php');

$cnt = Loader::controller("/install");
$cnt->on_start();
$fileWriteErrors = clone $cnt->fileWriteErrors;
$e = Loader::helper('validation/error');

// handle required items
if (!$cnt->get('imageTest')) {
	$e->add(t('GD library must be enabled to install concrete5.'));
}
if (!$cnt->get('mysqlTest')) {
	$e->add($cnt->getDBErrorMsg());
}
if (!$cnt->get('xmlTest')) {
	$e->add(t('SimpleXML and DOM must be enabled to install concrete5.'));
}
if (!$cnt->get('phpVtest')) {
	$e->add(t('concrete5 requires PHP 5.2 or greater.'));
}

if (is_object($fileWriteErrors)) {
	$e->add($fileWriteErrors);
}

$_POST['SAMPLE_CONTENT'] = $config['starting-point'];
$_POST['DB_SERVER'] = $config['db-server'];
$_POST['DB_USERNAME'] = $config['db-username'];
$_POST['DB_PASSWORD'] = $config['db-password'];
$_POST['DB_DATABASE'] = $config['db-database'];
$_POST['SITE'] = $config['site'];
$_POST['uPassword'] = $config['admin-password'];
$_POST['uPasswordConfirm'] = $config['admin-password'];
$_POST['uEmail'] = $config['admin-email'];

if (version_compare($APP_VERSION, APP_VERSION_CLI_MINIMUM, '<')) {
	$e->add('Your version of concrete5 must be at least ' . APP_VERSION_CLI_MINIMUM . ' to use this installer.');
}


if ($e->has()) {
	foreach($e->getList() as $ei) {
		print "ERROR: " . $ei . "\n";
	}	
	die;
}

$cnt->configure($e);

if ($e->has()) {
	foreach($e->getList() as $ei) {
		print "ERROR: " . $ei . "\n";
	}	
} else {
	$spl = Loader::startingPointPackage($config['starting-point']);
	require(DIR_CONFIG_SITE . '/site_install.php');
	require(DIR_CONFIG_SITE . '/site_install_user.php');
	$routines = $spl->getInstallRoutines();
	try {
		foreach($routines as $r) {
			print $r->getProgress() . '%: ' . $r->getText() . "\n";
			call_user_func(array($spl, $r->getMethod()));
		}
	} catch(Exception $ex) {
		print "ERROR: " . $ex->getMessage() . "\n";		
		$cnt->reset();
	}

	if ($config['demo-username']) {
		print "Adding demo user\n";
		Loader::model('userinfo');
		Loader::model('groups');
		UserInfo::add(array(
			'uName'            => $config['demo-username'],
			'uEmail'           => $config['demo-email'],
			'uPassword'        => $config['demo-password']
		))->getUserObject()->enterGroup(
			Group::getByID(ADMIN_GROUP_ID)
		);
	}

	if (!isset($ex)) {
		Config::save('SEEN_INTRODUCTION', 1);
		print "Installation Complete!\n";
	}


}
