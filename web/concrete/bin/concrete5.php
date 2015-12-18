<?php
$DIR_BASE_CORE = dirname(__DIR__);

if (stripos(PHP_OS, 'WIN') === 0) {
    foreach (array('PHP_SELF', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PATH_TRANSLATED') as $key) {
        // Check if the key is valid
        if (!isset($_SERVER[$key])) {
            continue;
        }
        $value = $_SERVER[$key];
        if (!is_file($value)) {
            continue;
        }
        // Just to simplify the regular expressions
        $value = str_replace('\\', '/', $value);
        // Check if the key is an absolute path
        if (preg_match('%^([A-Z]:/|//\w)%i', $value) !== 1) {
            continue;
        }
        if (preg_match('%/\.{1,2}/%', $value) !== 0) {
            continue;
        }
        // Ok!
        $DIR_BASE_CORE = dirname(dirname($value));
        break;
    }
    unset($key);
    unset($value);
}

define('DIR_BASE', dirname($DIR_BASE_CORE));

require $DIR_BASE_CORE . '/bootstrap/configure.php';
require $DIR_BASE_CORE . '/bootstrap/autoload.php';

if (!\Concrete\Core\Application\Application::isRunThroughCommandLineInterface()) {
    return false;
}

$cms = require $DIR_BASE_CORE . '/bootstrap/start.php';

$app = new \Concrete\Core\Console\Application();
$cms->instance('console', $app);
if ($cms->isInstalled()) {
    $cms->setupPackageAutoloaders();
    $cms->setupPackages();
}
$app->setupDefaultCommands();

\Events::dispatch('on_before_console_run');

$app->run();

\Events::dispatch('on_after_console_run');
