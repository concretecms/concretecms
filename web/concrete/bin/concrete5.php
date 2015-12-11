<?php
$DIR_BASE_CORE = dirname(__DIR__);
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
