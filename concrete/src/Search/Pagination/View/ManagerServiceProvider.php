<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('manager/view/pagination', function($app) {
            return new Manager($app);
        });
        $this->app->singleton('manager/view/pagination/pager', function($app) {
            return new PagerManager($app);
        });
    }
}
