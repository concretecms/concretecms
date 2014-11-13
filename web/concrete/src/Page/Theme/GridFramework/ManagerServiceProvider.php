<?php 
namespace Concrete\Core\Page\Theme\GridFramework;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{

	public function register()
    {
        $this->app['manager/grid_framework'] = $this->app->share(function($app) {
            return new Manager($app);
        });
	}
}