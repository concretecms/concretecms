<?php

/*
 * Bootstrap file for PHPStan: it boots the Concrete environment without processing any request (see README.md).
 */

const C5_ENVIRONMENT_ONLY = true;
define('DIR_BASE', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__)));

try {
    require DIR_BASE . '/concrete/dispatcher.php';
} finally {
    // Concrete unregisters the phar stream wrapper, but PHPStan needs it (see README.md)
    if (!in_array('phar', stream_get_wrappers(), true)) {
        stream_wrapper_restore('phar');
    }
}
// Let PHPStan handle errors and exceptions
restore_error_handler();
restore_exception_handler();
