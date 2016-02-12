<?php

if (!isset($DIR_BASE_CORE)) {
    // Try to detect the concrete directory starting from the current working directory
    // (useful with CLI scripts started from a common launcher)
    $dir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', getcwd()), '/');
    if (is_dir($dir.'/web')) {
        $dir .= '/web';
    }
    while (@is_dir($dir)) {
        if (is_file($dir.'/index.php') && is_file($dir.'/concrete/bootstrap/configure.php')) {
            $DIR_BASE_CORE = $dir.'/concrete';
            break;
        }
        $newDir = @dirname($dir);
        if ($newDir === $dir) {
            break;
        }
        $dir = $newDir;
    }
    unset($dir);
    unset($newDir);
}

if (!isset($DIR_BASE_CORE)) {
    // Try to detect the concrete directory starting from the filename of the currently executing script
    // (useful with symlinked concrete directories)
    foreach (array('PHP_SELF', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PATH_TRANSLATED') as $key) {
        // Check if the key is valid
        if (!isset($_SERVER[$key])) {
            continue;
        }
        $value = $_SERVER[$key];
        if (!is_file($value)) {
            continue;
        }
        if (stripos(PHP_OS, 'WIN') === 0) {
            // Just to simplify the regular expressions
            $value = str_replace('\\', '/', $value);
            // Check if the key is an absolute path
            if (preg_match('%^([A-Z]:/|//\w)%i', $value) !== 1) {
                continue;
            }
        } else {
            if (preg_match('%^/%i', $value) !== 1) {
                continue;
            }
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

if (!isset($DIR_BASE_CORE)) {
    // Fall back to the real directory containing this script
    $DIR_BASE_CORE = dirname(__DIR__);
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
