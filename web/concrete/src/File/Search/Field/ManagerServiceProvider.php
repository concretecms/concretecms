<?php
namespace Concrete\Core\File\Search\Field;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\File\Search\Field\Manager;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['manager/search_field/file'] = $this->app->share(function ($app) {
            return new Manager($app);
        });
    }
}
