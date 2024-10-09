<?php

declare(strict_types=1);

use Concrete\Core\Foundation\ClassAutoloader;

defined('C5_EXECUTE') or die('Access Denied.');

require_once DIR_APPLICATION . '/bootstrap/autoload.php';

$autoloader = ClassAutoloader::getInstance()->hook();

$appConfigPath = DIR_CONFIG_SITE . '/app.php';
if (file_exists($appConfigPath)) {
    $appConfig = require $appConfigPath;
    if (is_string($appConfig['namespace'] ?? null)) {
        $autoloader->setApplicationNamespace($appConfig['namespace']);
    }
    if (!empty($appConfig['enable_legacy_src_namespace'])) {
        $autoloader->setApplicationLegacyNamespaceEnabled(true);
    }
    unset($appConfig);
}
unset($appConfigPath);
unset($autoloader);
