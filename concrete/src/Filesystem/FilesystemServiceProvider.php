<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Filesystem\ElementManager;

/**
 * @since 8.2.0
 */
class FilesystemServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(ElementManager::class);

        $this->app->singleton('element', function($app) {
            return $app->make(ElementManager::class);
        });
    }


}
