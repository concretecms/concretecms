<?php
namespace Concrete\Core\Database;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Driver\DriverManager;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{

    public function register()
    {
        // Bind the driver manager config constructor
        $this->app->bind(
            'Concrete\Core\Database\Driver\DriverManager',
            function(Application $app) {
                $manager = new DriverManager($app);
                $manager->configExtensions($app['config']->get('database.drivers'));

                return $manager;
            });

        // Set DatabaseManager as a singleton, we want to be able to add managers to be available globally
        $this->app->singleton('Concrete\Core\Database\DatabaseManager');
        $this->app->bind('database', 'Concrete\Core\Database\DatabaseManager');

        // Do the same with ORM
        $this->app->singleton('Concrete\Core\Database\DatabaseManagerORM');
        $this->app->bind('database/orm', 'Concrete\Core\Database\DatabaseManagerORM');

        // Bind the structure manager
        $this->app->bind('database/structure', 'Concrete\Core\Database\DatabaseStructureManager');

        // Set the EntityManagerInterface to the default entitymanger
        $this->app->bind('Doctrine\ORM\EntityManagerInterface', function(Application $app) {
            return $app->make('Concrete\Core\Database\DatabaseManagerORM')->entityManager();
        });
        $this->app->bind('Doctrine\ORM\EntityManager', 'Doctrine\ORM\EntityManagerInterface');

        // Set the concrete Connection classname to the default connection
        $this->app->bind('Concrete\Core\Database\Connection\Connection', function(Application $app) {
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
            'Concrete\Core\Database\DatabaseManager',
            'Concrete\Core\Database\DatabaseManagerORM',
            'Doctrine\ORM\EntityManager',
            'Doctrine\ORM\EntityManagerInterface',
            'Concrete\Core\Database\Connection\Connection',
            'Doctrine\DBAL\Connection'
        );
    }

}
