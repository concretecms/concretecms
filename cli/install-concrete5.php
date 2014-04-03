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
define('C5_ENVIRONMENT_ONLY', true);

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 1);
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
		$target = realpath(dirname(__FILE__) . '/' . $target);
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


require $corePath . '/config/base_pre.php';
require $corePath . '/startup/config_check.php';
require $corePath . '/startup/updated_core_check.php';
require $corePath . '/config/base.php';
require $corePath . '/startup/autoload.php';
require $corePath . '/startup/helpers.php';
require $corePath . '/startup/file_permission_config.php';
require $corePath . '/startup/magic_quotes_gpc_check.php';
require $corePath . '/startup/timezone.php';
require $corePath . '/startup/file_access_check.php';
require $corePath . '/config/theme_paths.php';
require $corePath . '/config/routes.php';
require $corePath . '/config/class_aliases.php';
require $corePath . '/startup/url_check.php';
require $corePath . '/startup/encoding_check.php';
require $corePath . '/config/file_types.php';

if ($config['reinstall'] === 'yes') {

	// Remove all files from the files directory
	function removeDemoFiles($path) {
		global $target;
		$path .= end(str_split($path)) !== '/' ? '/' : '';
		foreach (glob($path . "*") as $file) {
			if (is_dir($file)) removeDemoFiles($file);
			if (is_file($file)) unlink($file);
		}
		$dirf = $target . '/files/';
		// Remove Directory once Files have been removed (If Exists)
		if (is_dir($path) && $path !== $dirf) rmdir($path); 
	}

	removeDemoFiles($target . '/files/');

	$db = Database::connect(array(
		'host' => $config['db-server'],
		'user' => $config['db-username'],
		'password' => $config['db-password'],
		'database' => $config['db-database']
	));

	$tables = $db->MetaTables();
	$sm = $db->getSchemaManager();
	foreach($tables as $table) {
		$sm->dropTable($table);
	}
}

$cnt = new \Concrete\Controller\Install();
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
	$spl = StartingPointPackage::getClass($config['starting-point']);
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
