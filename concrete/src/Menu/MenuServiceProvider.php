<?php

namespace Concrete\Core\Menu;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Menu\Type\Manager as MenuTypeManager;

class MenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            MenuTypeManager::class,
            function ($app) {
                $manager = new MenuTypeManager($app);
                $manager->driver('dashboard');
                return $manager;
            }
        );

    }
}
