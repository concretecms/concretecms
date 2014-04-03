<?php
/**
 * @author jshannon
 * @author Andrew Embler
 */

// testing credentials
define('DB_USERNAME', 'phpunitTesting');
define('DB_PASSWORD', 'phpunitTesting');
define('DB_DATABASE', 'phpunitTesting');
define('DB_SERVER', 'localhost');

// TODO: check include path
//ini_set('include_path', ini_get('include_path'));

define('C5_EXECUTE', true);
define('C5_ENVIRONMENT_ONLY', true);
$DIR_BASE_CORE = realpath(dirname(__FILE__) . '/../../web/concrete');

require $DIR_BASE_CORE . '/config/base_pre.php';
require $DIR_BASE_CORE . '/startup/config_check.php';
require $DIR_BASE_CORE . '/startup/updated_core_check.php';
require $DIR_BASE_CORE . '/config/base.php';
require $DIR_BASE_CORE . '/startup/autoload.php';

$app = Concrete\Core\Application\Dispatcher::get();
$app->bootstrap();
/*
//causes dispatcher to skip the page rendering
define('C5_ENVIRONMENT_ONLY', true);

//prevents dispatcher from causing redirection to the base_url
define('REDIRECT_TO_BASE_URL', false);

//since we can't define/redefine this for individual tests, we set to a value that's most likely to cause errors (vs '')
define('DIR_REL', '/blog');

//this is where the magic happens
require(DIR_BASE . '/concrete/dispatcher.php');

// login the admin
User::getByUserID(1, true);
Log::addEntry('bootsrapped','unit tests');

// include adodb-lib to avoid a PHPUnit problem with globals
include(ADODB_DIR.'/adodb-lib.inc.php');
*/
