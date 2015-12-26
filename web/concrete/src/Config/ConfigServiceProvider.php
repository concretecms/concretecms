4<?php
namespace Concrete\Core\Config;

use Concrete\Core\Foundation\Service\Provider;

class ConfigServiceProvider extends Provider
{
    /**
     * Configuration repositories
     * @return void
     */
    public function register()
    {
        $this->registerFileConfig();
        $this->registerDatabaseConfig();
    }

    private function registerFileConfig()
    {
        // Bind abstract type
        $this->app->singleton('config', function($app) {
            $loader = $app->make('Concrete\Core\Config\FileLoader');
            $saver = $app->make('Concrete\Core\Config\FileSaver');

            return $app->build('Concrete\Core\Config\Repository\Repository', array($loader, $saver, $app->environment()));
        });

        // Bind the concrete type
        $this->app->bind('Concrete\Core\Config\Repository\Repository', 'config');
        $this->app->bind('Illuminate\Config\Repository', 'Concrete\Core\Config\Repository\Repository');
    }

    private function registerDatabaseConfig()
    {
        $this->app->bindShared('config/database', function($app) {
            $loader = $app->make('Concrete\Core\Config\DatabaseLoader');
            $saver = $app->make('Concrete\Core\Config\DatabaseSaver');

            return $app->build('Concrete\Core\Config\Repository\Repository', array($loader, $saver, $app->environment()));
        });
    }

}
