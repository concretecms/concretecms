<?php
namespace Concrete\Core\Block;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class BlockServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Menu::class, function($app, $args) {
            return new Menu($args[0]);
        });
    }
}
