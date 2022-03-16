<?php
namespace Concrete\Core\Config;

use Concrete\Core\Foundation\Service\Provider;

class ConfigServiceProvider extends Provider
{
    /**
     * Configuration repositories.
     */
    public function register()
    {
        $this->registerFileConfig();
        $this->registerDatabaseConfig();
    }

    /**
     * Create a file config repository.
     */
    private function registerFileConfig()
    {
        $this->app->bindIf(LoaderInterface::class, static function($app) {
            return $app->make(CompositeLoader::class, ['app' >= $app, 'loaders' => [
                CoreFileLoader::class,
                FileLoader::class,
            ]]);
        });
        $this->app->bindIf(SaverInterface::class, FileSaver::class);

        $this->app->singleton(Repository\Repository::class, static function ($app) {
            $loader = $app->make(LoaderInterface::class);
            $saver = $app->make(SaverInterface::class);
            return new Repository\Repository($loader, $saver, $app->environment());
        });
        $this->app->alias(Repository\Repository::class, 'config');
        $this->app->alias(Repository\Repository::class, \Illuminate\Config\Repository::class);
    }

    /**
     * Create a database config repository.
     */
    private function registerDatabaseConfig()
    {
        $this->app->bindShared('config/database', function ($app) {
            $loader = $app->make('Concrete\Core\Config\DatabaseLoader');
            $saver = $app->make('Concrete\Core\Config\DatabaseSaver');
            return new Repository\Repository($loader, $saver, $app->environment());
        });
    }
}
