<?php

/*
 * Bootstrap file for PHPStan.
 * It boots the Concrete environment (constants, autoloaders, class aliases, ...)
 * without actually processing any request.
 */

const C5_ENVIRONMENT_ONLY = true;
define('DIR_BASE', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));

require DIR_BASE . '/concrete/dispatcher.php';

// Concrete unregisters the phar stream wrapper, but PHPStan needs it when running from a .phar file
if (!in_array('phar', stream_get_wrappers(), true)) {
    stream_wrapper_restore('phar');
}
// Let PHPStan handle errors and exceptions
restore_error_handler();
restore_exception_handler();
