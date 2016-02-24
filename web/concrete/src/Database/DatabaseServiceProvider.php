<?php
namespace Concrete\Core\Database;

use Concrete\Core\Database\Driver\DriverManager;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{

    public function register()
    {
        // Make both managers singletons
        $this->app->singleton('Concrete\Core\Database\DatabaseManager');
        $this->app->singleton('Concrete\Core\Database\DatabaseManagerORM');

        // Bind both `database` and `database/orm` to their respective classes
        $this->app->bind('database', 'Concrete\Core\Database\DatabaseManager');
        $this->app->bind('database/orm', 'Concrete\Core\Database\DatabaseManagerORM');

        // Bind a constructor for our DriverManager bootstrapped from config
        $this->app->bind('Concrete\Core\Database\Driver\DriverManager', function ($app) {
            $manager = new DriverManager($app);
            $manager->configExtensions($app->make('config')->get('database.drivers'));
            return $manager;
        });

        // Bind a closure to support \Core::make('database/structure', $em);
        $this->app->bind('database/structure', function ($app, $em) {
            if (!is_array($em)) {
                $em = array($em);
            }

            return $app->make('Concrete\Core\Database\DatabaseStructureManager', $em);
        });

        // Bind default entity manager resolver
        $this->app->bind('Doctrine\ORM\EntityManagerInterface', function ($app) {
            return $app->make('Concrete\Core\Database\DatabaseManagerORM')->entityManager();
        });
        $this->app->bind('Doctrine\ORM\EntityManager', 'Doctrine\ORM\EntityManagerInterface');

        // Bind default connection resolver
        $this->app->bind('Concrete\Core\Database\Connection\Connection', function ($app) {
            return $app->make('Concrete\Core\Database\DatabaseManager')->connection();
        });
        $this->app->bind('Doctrine\DBAL\Connection', 'Concrete\Core\Database\Connection\Connection');
    }

    /**
     * A list of things that this service provider provides
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'database',
            'database/orm',
            'database/structure',
            'Concrete\Core\Database\Driver\DriverManager',
            'Doctrine\ORM\EntityManager',
            'Doctrine\ORM\EntityManagerInterface',
            'Concrete\Core\Database\Connection\Connection',
            'Doctrine\DBAL\Connection'
        );
    }

}
