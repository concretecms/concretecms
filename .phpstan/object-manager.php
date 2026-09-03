<?php

/*
 * Object manager loader for phpstan-doctrine.
 * It returns the Doctrine EntityManager of Concrete, so that PHPStan knows the entity metadata.
 * The database is never accessed: if it's not configured (for example in the CI), we configure a fake one.
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
        // Lets Doctrine know the database platform without connecting to the server
        'serverVersion' => '8.0',
        'character_set' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ]);
}

return $app->make(EntityManagerInterface::class);
