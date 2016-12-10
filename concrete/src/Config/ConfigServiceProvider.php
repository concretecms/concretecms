<?php
namespace Concrete\Core\Config;

use Concrete\Core\Config\Repository\Repository;
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

        // Bind the concrete types
        $this->app->bind('Concrete\Core\Config\Repository\Repository', 'config');
        $this->app->bind('Illuminate\Config\Repository', 'Concrete\Core\Config\Repository\Repository');
    }

    /**
     * Create a file config repository.
     */
    private function registerFileConfig()
    {
        $provider = $this;
        $this->app->singleton('config', function ($app) use ($provider) {
            $loader = $app->make('Concrete\Core\Config\FileLoader');
            $saver = $app->make('Concrete\Core\Config\FileSaver');

            /** @var Repository $repository */
            $repository = $app->build('Concrete\Core\Config\Repository\Repository', array($loader, $saver, $app->environment()));
            return $provider->applyAfterLoad($repository);
        });
    }

    /**
     * Create a database config repository.
     */
    private function registerDatabaseConfig()
    {
        $this->app->bindShared('config/database', function ($app) {
            $loader = $app->make('Concrete\Core\Config\DatabaseLoader');
            $saver = $app->make('Concrete\Core\Config\DatabaseSaver');

            return $app->build('Concrete\Core\Config\Repository\Repository', array($loader, $saver, $app->environment()));
        });
    }

    private function applyAfterLoad(Repository $repository)
    {
        // Shim `concrete.site` using the default site name
        $repository->afterLoading(null, function($repo, $group, $items) {
            // If we're loading the "concrete" group "config/concrete.php"
            if ($group == 'concrete') {
                // And there's no site defined
                if (!array_get($items, 'site')) {
                    $site = $repo->get('site.default');

                    // Set the site to whatever is set in the site config
                    $items['site'] = $repo->get("site.sites.{$site}.name");
                }
            }

            return $items;
        });

        return $repository;
    }
}
