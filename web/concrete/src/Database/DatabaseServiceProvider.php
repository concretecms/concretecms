<?php
namespace Concrete\Core\Database;

use Concrete\Core\Database\Driver\DriverManager;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Package\PackageList;
use Doctrine\ORM\EntityManagerInterface;

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

        // Bind default connection resolver
        $this->app->bind('Concrete\Core\Database\Connection\Connection', function ($app) {
            return $app->make('Concrete\Core\Database\DatabaseManager')->connection();
        });
        $this->app->bind('Doctrine\DBAL\Connection', 'Concrete\Core\Database\Connection\Connection');

        // Bind default entity manager resolver
        $this->app->bindShared('Doctrine\ORM\EntityManagerInterface', function ($app) {
            $factory = new EntityManagerFactory();
            $entityManager = $factory->create($app->make('Concrete\Core\Database\Connection\Connection'));
            if ($this->app->isInstalled()) {
                $this->setupPackageEntityManagers($entityManager);
            }
            return $entityManager;
        });
        $this->app->bind('Doctrine\ORM\EntityManager', 'Doctrine\ORM\EntityManagerInterface');

    }


    protected function setupPackageEntityManagers(EntityManagerInterface $entityManager)
    {
        try {
            $packages = $entityManager->getRepository('Concrete\Core\Entity\Package')
                ->findAll();
        } catch (\Exception $e) {
            $packages = array();
        }

        $driver = $entityManager->getConfiguration()->getMetadataDriverImpl();

        $paths = array();

        foreach($packages as $package) {
            $class = $package->getController();
            $paths = array_merge($paths, $class->getPackageEntityPaths());
        }

        $driver->addPaths($paths);
    }


    /**
     * A list of things that this service provider provides.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'database',
            'database/orm',
            'Concrete\Core\Database\Driver\DriverManager',
            'Doctrine\ORM\EntityManager',
            'Doctrine\ORM\EntityManagerInterface',
            'Concrete\Core\Database\Connection\Connection',
            'Doctrine\DBAL\Connection',
        );
    }
}
