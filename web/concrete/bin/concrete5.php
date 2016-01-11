<?php

$DIR_BASE_CORE = dirname(__DIR__);

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

define('DIR_BASE', dirname($DIR_BASE_CORE));

$cms = require $DIR_BASE_CORE . '/dispatcher.php';
