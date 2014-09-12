<?php
/**
 * @author Andrew Embler
 */

// testing credentials
define('DB_USERNAME', 'travis');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'concrete5_tests');
define('DB_SERVER', 'localhost');

define('BASE_URL', 'http://www.dummyco.com');
define('URL_REWRITING', false);
define('DIR_BUILDTOOLS', dirname(dirname(__FILE__)) . '/build-tools');
if(!is_dir(DIR_BUILDTOOLS)) {
	exec('git clone --depth 1 --single-branch --branch master https://github.com/mlocati/concrete5-build ' . escapeshellarg(DIR_BUILDTOOLS));
}

// error reporting
PHPUnit_Framework_Error_Notice::$enabled = FALSE;
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));

require_once('ConcreteDatabaseTestCase.php');
require_once('BlockTypeTestCase.php');
require_once('PageTestCase.php');
require_once('AttributeTestCase.php');
require_once('FileStorageTestCase.php');
require_once('UserTestCase.php');


define('DIR_BASE', realpath(dirname(__FILE__) . '/../../web'));
$DIR_BASE_CORE = realpath(dirname(__FILE__) . '/../../web/concrete');

require $DIR_BASE_CORE . '/bootstrap/configure.php';

/**
 * Include all autoloaders
 */
require $DIR_BASE_CORE . '/bootstrap/autoload.php';

/**
 * Begin concrete5 startup.
 */
$cms = require $DIR_BASE_CORE . '/bootstrap/start.php';

/**
 * Kill this because it plays hell with phpunit.
 */
unset($cms);