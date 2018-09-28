<?php

namespace Concrete\Core\Database;

use Concrete\Core\Database\Driver\DriverManager;
use Concrete\Core\Database\Query\LikeBuilder;
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
        $this->app->bind('database/orm',
            'Concrete\Core\Database\DatabaseManagerORM');

        // Bind a constructor for our DriverManager bootstrapped from config
        $this->app->bind('Concrete\Core\Database\Driver\DriverManager',
            function ($app) {
            $manager = new DriverManager($app);
            $manager->configExtensions($app->make('config')->get('database.drivers'));

            return $manager;
        });

        // Bind default connection resolver
        $this->app->bind('Concrete\Core\Database\Connection\Connection',
            function ($app) {
            return $app->make('Concrete\Core\Database\DatabaseManager')->connection();
        });
        $this->app->bind('Doctrine\DBAL\Connection',
            'Concrete\Core\Database\Connection\Connection');


        // Bind EntityManager factory
        $this->app->bind('Concrete\Core\Database\EntityManagerConfigFactory',
            function($app) {
            $config = $app->make('Doctrine\ORM\Configuration');
            $configRepository = $app->make('config');
            $connection = $app->make('Doctrine\DBAL\Connection');
            return new EntityManagerConfigFactory($app, $config, $configRepository, $connection);
        });
        $this->app->bind('Concrete\Core\Database\EntityManagerConfigFactoryInterface',
            'Concrete\Core\Database\EntityManagerConfigFactory');

        $this->app->bind('Concrete\Core\Database\EntityManagerFactory',
            function ($app) {
            $configFactory = $app->make('Concrete\Core\Database\EntityManagerConfigFactory');
            return new EntityManagerFactory($configFactory);
        });
        $this->app->bind('Concrete\Core\Database\EntityManagerFactoryInterface',
            'Concrete\Core\Database\EntityManagerFactory');

        // Bind default entity manager resolver
        $this->app->singleton('Doctrine\ORM\EntityManager',
            function ($app) {
            $factory = $app->make('Concrete\Core\Database\EntityManagerFactory');
            $entityManager = $factory->create($app->make('Concrete\Core\Database\Connection\Connection'));
            return $entityManager;
        });
        $this->app->bind('Doctrine\ORM\EntityManagerInterface',
            'Doctrine\ORM\EntityManager');

        // ------------------------------------------
        // Bind Doctrine EntityManager setup classes
        $this->app->bind('Doctrine\Common\Cache\ArrayCache',
            function() {
            return new \Doctrine\Common\Cache\ArrayCache();
        });
        $this->app->bind('Doctrine\Common\Annotations\AnnotationReader',
            function() {
            return new \Doctrine\Common\Annotations\AnnotationReader();
        });
        $this->app->bind('Doctrine\Common\Annotations\SimpleAnnotationReader',
            function() {
            return new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        });
        $this->app->bind('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            function() {
            return new \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();
        });
        // ORM Cache
        $this->app->bind('orm/cache',
            function($app) {
            // Set cache based on doctrine dev mode
            $isDevMode = $app->make('config')->get('concrete.cache.doctrine_dev_mode');
            if ($isDevMode) {
                $cache = $this->app->make('Doctrine\Common\Cache\ArrayCache');
            } else {
                $cache = new \Concrete\Core\Cache\Adapter\DoctrineCacheDriver('cache/expensive');
            }
            return $cache;
        });

        // Bind Doctrine ORM config resolver
        $this->app->bind('Doctrine\ORM\Configuration',
            function($app) {
            $isDevMode = $app->make('config')->get('concrete.cache.doctrine_dev_mode');
            $proxyDir  = $app->make('config')->get('database.proxy_classes');
            $cache     = $app->make('orm/cache');
            $config    = \Doctrine\ORM\Tools\Setup::createConfiguration(
                $isDevMode, $proxyDir, $cache);

            foreach($app->make('config')->get('app.entity_namespaces') as $namespace => $class) {
                $config->addEntityNamespace($namespace, $class);
            }
            return $config;
        });

        // Create the annotation reader used by packages and core > c5 version 8.0.0
        // Accessed by PackageService and the EntityManagerConfigFactory
        $this->app->bind('orm/cachedAnnotationReader',
            function($app) {
                $annotationReader = $app->make('Doctrine\Common\Annotations\AnnotationReader');
                return new \Doctrine\Common\Annotations\CachedReader($annotationReader,
                    $app->make('orm/cache'));
            });

        // Create legacy annotation reader used package requiring concrete5
        // version lower than 8.0.0
        // Accessed by PackageService and the EntityManagerConfigFactory
        $this->app->bind('orm/cachedSimpleAnnotationReader',
            function($app) {
                $simpleAnnotationReader = $this->app->make('Doctrine\Common\Annotations\SimpleAnnotationReader');
                $simpleAnnotationReader->addNamespace('Doctrine\ORM\Mapping');
                return new \Doctrine\Common\Annotations\CachedReader($simpleAnnotationReader,
                    $app->make('orm/cache'));
            });

        // Setup doctrine proxy autoloader
        if ($this->app->bound('config')) {
            $proxyDir = $this->app->make('config')->get('database.proxy_classes');
            $proxyNamespace = "DoctrineProxies";
            \Doctrine\Common\Proxy\Autoloader::register($proxyDir, $proxyNamespace);
        }
        // Other helpers
        $this->app->when(LikeBuilder::class)
            ->needs('$otherWildcards')
            ->give(function($app) {
                $otherWildcards = [];
                if ($app->bound('Concrete\Core\Database\Connection\Connection')) {
                    $connection = $app->make('Concrete\Core\Database\Connection\Connection');
                    $platform = $connection->getDatabasePlatform();
                    $platformWildcards = $platform->getWildcards();
                    $otherWildcards = array_values(array_diff($platformWildcards, [LikeBuilder::DEFAULT_ANYCHARACTER_WILDCARD, LikeBuilder::DEFAULT_ONECHARACTER_WILDCARD]));
                }
                return $otherWildcards;
            })
        ;
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
            'orm/cache',
            'orm/cachedAnnotationReader',
            'orm/cachedSimpleAnnotationReader',
            'Concrete\Core\Database\Driver\DriverManager',
            'Doctrine\ORM\EntityManager',
            'Doctrine\ORM\EntityManagerInterface',
            'Concrete\Core\Database\Connection\Connection',
            'Doctrine\DBAL\Connection',
            'Doctrine\ORM\Configuration',
            'Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            LikeBuilder::class,
        );
    }
}