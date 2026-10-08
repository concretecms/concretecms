<?php

use Concrete\Core\Http\Request;
use Concrete\TestHelpers\Http\FakeHttpsServer;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Error\Notice;

// Fix for phpstorm + tests run in separate processes
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../concrete/vendor/autoload.php');
}

// Define test constants
putenv('CONCRETE5_ENV=ccm_test');
define('DIR_TESTS', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));
define('DIR_BASE', dirname(DIR_TESTS));
define('BASE_URL', 'http://www.dummyco.com/path/to/server');

// Every test run has its own database and temporary directory, so that more runs can work side by side
$runID = getenv('CCM_TESTS_RUNID');
if ($runID === false || $runID === '') {
    $runID = '1';
} elseif (!preg_match('/^[1-9][0-9]{0,7}$/', $runID)) {
    throw new Exception('CCM_TESTS_RUNID must be a positive integer');
}
define('CCM_TESTS_RUNID', (int) $runID);
define('CCM_TESTS_DBNAME', CCM_TESTS_RUNID === 1 ? 'ccm_tests' : 'ccm_tests' . CCM_TESTS_RUNID);
define('CCM_TESTS_TEMPDIR', DIR_TESTS . '/tmp/run' . CCM_TESTS_RUNID);

// This file is loaded again by the child processes of PHPUnit (tests marked with @runInSeparateProcess) and of some
// tests: they must not reset the database and the temporary directory the parent is using, so the parent marks them
define('CCM_TESTS_MAIN_PROCESS', getenv('CCM_TESTS_BOOTSTRAPPED') !== '1');

// The application and packages directories are copies too, so that the files Concrete and the tests write there stay per process
define('DIR_APPLICATION', CCM_TESTS_TEMPDIR . '/application');
define('DIR_PACKAGES', CCM_TESTS_TEMPDIR . '/packages');

// Define concrete5 constants
require DIR_BASE . '/concrete/bootstrap/configure.php';

if (CCM_TESTS_MAIN_PROCESS) {
    // Start with an empty temporary directory, containing the log, an empty packages directory and an application
    // directory built from tests/assets/application: the tests must not depend on what the real one contains,
    // except for its bootstrap files
    $fs = new Filesystem();
    if ($fs->isDirectory(CCM_TESTS_TEMPDIR)) {
        $fs->deleteDirectory(CCM_TESTS_TEMPDIR, true);
    } else {
        $fs->makeDirectory(CCM_TESTS_TEMPDIR, 0777, true);
    }
    $fs->makeDirectory(CCM_TESTS_TEMPDIR . '/logs', 0777, true);
    $fs->makeDirectory(DIR_PACKAGES, 0777, true);
    if ($fs->copyDirectory(DIR_TESTS . '/assets/application', DIR_APPLICATION) !== true) {
        throw new Exception('Failed to copy the test application directory to ' . DIR_APPLICATION);
    }
    foreach ([
        'attributes', 'authentication', 'blocks', 'bootstrap', 'controllers', 'elements', 'files/cache', 'files/incoming',
        'files/tmp', 'images', 'jobs', 'languages', 'mail', 'page_templates', 'single_pages', 'src', 'themes', 'tools',
    ] as $directory) {
        $fs->makeDirectory(DIR_APPLICATION . '/' . $directory, 0777, true);
    }
    foreach ($fs->files(DIR_BASE . '/' . DIRNAME_APPLICATION . '/bootstrap') as $file) {
        if (!$fs->copy($file, DIR_APPLICATION . '/bootstrap/' . basename($file))) {
            throw new Exception('Failed to copy ' . $file . ' to ' . DIR_APPLICATION . '/bootstrap');
        }
    }
}

// Include all autoloaders.
require DIR_BASE_CORE . '/bootstrap/autoload.php';

if (CCM_TESTS_MAIN_PROCESS) {
    // Create an empty database before starting Concrete, since it connects to it while booting
    $dbConfig = require DIR_CONFIG_SITE . '/database.php';
    $dbConfig = $dbConfig['connections'][$dbConfig['default-connection']];
    try {
        $cn = new PDO("mysql:host={$dbConfig['server']};charset={$dbConfig['charset']}", $dbConfig['username'], $dbConfig['password']);
    } catch (PDOException $x) {
        throw new Exception('Unable to connect to the test database server with the credentials set in ' . DIR_TESTS . '/assets/application/config/database.php', 0, $x);
    }
    $cn->exec('DROP DATABASE IF EXISTS ' . CCM_TESTS_DBNAME);
    $cn->exec('CREATE DATABASE ' . CCM_TESTS_DBNAME);

    putenv('CCM_TESTS_BOOTSTRAPPED=1');
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

if (CCM_TESTS_MAIN_PROCESS) {
    // Start the fake HTTPS server used by the tests that perform real HTTPS requests, and stop it when we quit.
    // If it can't be started, the tests requiring it will be skipped (see FakeHttpsServer::getStartupError()).
    FakeHttpsServer::tryStart();
    register_shutdown_function(static function () {
        FakeHttpsServer::shutdown();
    });
}

// Unset variables, so that PHPUnit won't consider them as global variables.
unset(
    $runID,
    $fs,
    $dbConfig,
    $cn,
    $app
);
