<?php

namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Application\Application;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            Manager::class,
            static function(Application $app): Manager {
                $manager = new Manager($app);
                $manager->register($app->make(UserProvider::class));
                $manager->register($app->make(ActiveThemeProvider::class));
                return $manager;
            }
        );
        $this->app->alias(Manager::class, 'manager/area_layout_preset_provider');
    }
}
