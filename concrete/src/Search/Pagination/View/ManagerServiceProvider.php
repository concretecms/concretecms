<?php
namespace Concrete\Core\Search\Pagination\View;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app['manager/view/pagination'] = $this->app->share(function($app) {
            return new Manager($app);
        });
    }
}