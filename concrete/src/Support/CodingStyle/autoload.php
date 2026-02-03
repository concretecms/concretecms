<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle;

defined('C5_EXECUTE') or die('Access Denied.');

/*
 * We can't use the Concrete autoloader because:
 * - the classes in the Concrete\Core\Support\CodingStyle namespace are used by PHP CS Fixer
 * - PHP CS Fixer has its own versions of composer packages that may conflict with the Concrete ones
 */

spl_autoload_register(
    static function (string $class) {
        if (strpos($class, __NAMESPACE__ . '\\') !== 0) {
            return;
        }
        $file = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen(__NAMESPACE__))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    },
);

// @php-cs-fixer-ignore modernize_strpos
