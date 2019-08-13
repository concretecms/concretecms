<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

/**
 * @since 5.7.4
 */
class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['manager/view/pagination'] = $this->app->share(function ($app) {
            return new Manager($app);
        });
        $this->app['manager/view/pagination/pager'] = $this->app->share(function ($app) {
            return new PagerManager($app);
        });
    }
}
