<?php
/**
 * @author Andrew Embler
 */

// testing credentials

use Concrete\Core\Config\Repository;

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

define('DIR_BASE', realpath(dirname(__FILE__) . '/../../web'));
$DIR_BASE_CORE = realpath(dirname(__FILE__) . '/../../web/concrete');

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
    ));

$config->get('concrete');
$config->set('concrete.cache.blocks', false);
$config->set('concrete.cache.pages', false);
$config->set('concrete.cache.enabled', false);

/**
 * Kill this because it plays hell with phpunit.
 */
unset($cms);
