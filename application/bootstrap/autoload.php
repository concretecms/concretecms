<?php

declare(strict_types=1);

defined('C5_EXECUTE') or die('Access Denied.');

// If the follwing class is already provided, we are likely in a composer-based Concrete installation
if (!class_exists(Illuminate\Container\Container::class)) {
    // Otherwise, let's try to load composer ourselves
    if (!@include DIR_BASE_CORE . '/' . DIRNAME_VENDOR . '/autoload.php') {
        echo 'Third party libraries not installed. Make sure that composer has required libraries in the concrete/ directory.';
        exit(1);
    }
}
