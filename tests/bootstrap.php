<?php

use Concrete\Core\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Concrete\TestHelpers\Config\Fixtures\TestFileSaver;
use Concrete\TestHelpers\Config\Fixtures\TestFileLoader;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Config;

// Define test constants
define('DIR_TESTS', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));
define('DIR_BASE', dirname(DIR_TESTS));
define('BASE_URL', 'http://www.dummyco.com/path/to/server');

// Define concrete5 constants
require DIR_BASE . '/concrete/bootstrap/configure.php';

// Include all autoloaders.
require DIR_BASE_CORE . '/bootstrap/autoload.php';

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
$cms = require DIR_BASE_CORE . '/bootstrap/start.php';
/* @var Concrete\Core\Application\Application $cms */

// Configure error reporting (test more strictly than core settings)
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
PHPUnit_Framework_Error_Notice::$enabled = false;

// Configure the configuration system
$files = new Filesystem();
$config = new Repository(new TestFileLoader($files), new TestFileSaver($files), 'travis');
$cms->instance('config', $config);
Config::clearResolvedInstance('config');

// Disable caches
$config->get('concrete');
$config->set('concrete.cache.blocks', false);
$config->set('concrete.cache.pages', false);
$config->set('concrete.cache.enabled', false);
$config->set('concrete.user.password.hash_cost_log2', 1);

// Initialize the database
$cn = $cms->make('database')->connection('travisWithoutDB');
$cn->connect();
if (!$cn->isConnected()) {
    throw new Exception('Unable to connect to test database, please create a user "travis" with no password with full privileges to a database "concrete5_tests"');
}
$cn->query('DROP DATABASE IF EXISTS concrete5_tests');
$cn->query('CREATE DATABASE concrete5_tests');
$cn->close();
