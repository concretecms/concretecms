<?php

use Concrete\Core\Http\Request;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Error\Notice;

// Fix for phpstorm + tests run in separate processes
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../concrete/vendor/autoload.php');
}

// Define test constants
putenv('CONCRETE5_ENV=ccm_test');
define('DIR_TESTS', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));
define('DIR_CONFIG_SITE', DIR_TESTS . '/config');
define('DIR_BASE', dirname(DIR_TESTS));
define('BASE_URL', 'http://www.dummyco.com/path/to/server');

// Define concrete5 constants
require DIR_BASE . '/concrete/bootstrap/configure.php';

// Include all autoloaders.
require DIR_BASE_CORE . '/bootstrap/autoload.php';

// Reset the configuration environment
$fs = new Filesystem();
if ($fs->isDirectory(DIR_CONFIG_SITE . '/generated_overrides')) {
    $fs->deleteDirectory(DIR_CONFIG_SITE . '/generated_overrides', true);
} else {
    $fs->makeDirectory(DIR_CONFIG_SITE . '/generated_overrides', 0777, true);
}
$fs->put(DIR_CONFIG_SITE . '/generated_overrides/.gitignore', '');
if ($fs->isDirectory(DIR_CONFIG_SITE . '/doctrine')) {
    $fs->deleteDirectory(DIR_CONFIG_SITE . '/doctrine');
}

// Define a fake request
Request::setInstance(new Request(
    [],
    [],
    [],
    [],
    [],
    ['HTTP_HOST' => 'www.requestdomain.com', 'SCRIPT_NAME' => '/path/to/server/index.php']
));

// Begin concrete5 startup.
$app = require DIR_BASE_CORE . '/bootstrap/start.php';
/* @var Concrete\Core\Application\Application $app */

// Initialize the database
$cn = $app->make('database')->connection('ccm_testWithoutDB');
$cn->connect();
if (!$cn->isConnected()) {
    throw new Exception('Unable to connect to test database, please create a user "ccm_test" with no password with full privileges to a database "ccm_tests"');
}
$cn->query('DROP DATABASE IF EXISTS ccm_tests');
$cn->query('CREATE DATABASE ccm_tests');
$cn->close();

// Unset variables, so that PHPUnit won't consider them as global variables.
unset(
    $app,
    $fs,
    $cn
);
