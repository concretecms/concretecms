<?php
/**
 * @author Andrew Embler
 */

// testing credentials

use Concrete\Core\Config\Repository\Repository;

define('DIR_BUILDTOOLS', dirname(dirname(__FILE__)) . '/build-tools');
if (!is_dir(DIR_BUILDTOOLS)) {
    exec(
        'git clone --depth 1 --single-branch --branch master https://github.com/mlocati/concrete5-build ' . escapeshellarg(
            DIR_BUILDTOOLS));
}

// error reporting
PHPUnit_Framework_Error_Notice::$enabled = false;

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));

require_once('ConcreteDatabaseTestCase.php');
require_once('BlockTypeTestCase.php');
require_once('PageTestCase.php');
require_once('AttributeTestCase.php');
require_once('FileStorageTestCase.php');
require_once('UserTestCase.php');

define('DIR_BASE', realpath(__DIR__ . '/../../'));
$DIR_BASE_CORE = realpath(DIR_BASE . '/concrete');

require $DIR_BASE_CORE . '/bootstrap/configure.php';

/**
 * Include all autoloaders
 */
require $DIR_BASE_CORE . '/bootstrap/autoload.php';

$r = new \Concrete\Core\Http\Request(
    array(),
    array(),
    array(),
    array(),
    array(),
    array('HTTP_HOST' => 'www.dummyco.com', 'SCRIPT_NAME' => '/path/to/server/index.php')
);
\Concrete\Core\Http\Request::setInstance($r);

/**
 * Begin concrete5 startup.
 */
$cms = require $DIR_BASE_CORE . '/bootstrap/start.php';

/**
 * Test more strictly than core settings
 */
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);


class TestConfigRepository extends Repository {

    public function save($key, $value)
    {
        return true;
    }

}

$old_config = $cms->make('config');
$cms->instance('config', new TestConfigRepository($old_config->getLoader(), $old_config->getSaver(), 'travis'));
\Concrete\Core\Support\Facade\Config::clearResolvedInstance('config');
\Config::set('concrete.seo.canonical_url', null);

/** @var Repository $config */
$config = $cms->make('config');
$config->set('database.default-connection', 'travis');
$config->set(
    'database.connections.travis',
    array(
        'driver' => 'c5_pdo_mysql',
        'server' => 'localhost',
        'database' => 'concrete5_tests',
        'username' => 'travis',
        'password' => '',
        'charset' => 'utf8'
    )
);
$config->set(
    'database.connections.travisWithoutDB',
    array(
        'driver' => 'c5_pdo_mysql',
        'server' => 'localhost',
        'username' => 'travis',
        'password' => '',
        'charset' => 'utf8'
    )
);

$config->get('concrete');
$config->set('concrete.cache.blocks', false);
$config->set('concrete.cache.pages', false);
$config->set('concrete.cache.enabled', false);

/** @var Concrete\Core\Database\Connection\Connection $cn */
$cn = $cms->make('database')->connection('travisWithoutDB');
$cn->connect();
if (!$cn->isConnected()) {
    throw new \Exception('Unable to connect to test database, please create a user "travis" with no password with full privileges to a database "concrete5_tests"');
}

$cn->query('DROP DATABASE IF EXISTS concrete5_tests');
$cn->query('CREATE DATABASE concrete5_tests');
$cn->close();

/**
 * Kill this because it plays hell with phpunit.
 */
unset($cms);
