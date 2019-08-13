<?php
namespace Concrete\Core\Block;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

/**
 * @since 8.0.0
 */
class BlockServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Menu::class, function($app, $args) {
            return new Menu($args[0]);
        });
    }
}
