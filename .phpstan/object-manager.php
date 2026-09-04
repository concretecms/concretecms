<?php

/*
 * Object manager loader for phpstan-doctrine: it returns the Doctrine EntityManager of Concrete (see README.md).
 */

use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

if (!defined('DIR_BASE')) {
    require __DIR__ . '/bootstrap.php';
}

$app = Application::getFacadeApplication();
$config = $app->make('config');
if (!$config->get('database.default-connection')) {
    $config->set('database.default-connection', 'concrete');
    $config->set('database.connections.concrete', [
        'driver' => 'c5_pdo_mysql',
        'server' => 'localhost',
        'database' => 'phpstan',
        'username' => 'phpstan',
        'password' => '',
        // Let Doctrine know the database platform without connecting to the server
        'serverVersion' => '8.0',
        'character_set' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ]);
}

return $app->make(EntityManagerInterface::class);
