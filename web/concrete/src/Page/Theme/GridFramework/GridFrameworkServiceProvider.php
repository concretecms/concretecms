<?php 
namespace Concrete\Core\Page\Theme\GridFramework;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class GridFrameworkServiceProvider extends ServiceProvider
{

	public function register()
    {
        $this->app['grid_framework'] = $this->app->share(function($app) {
            return new Manager($app);
        });
	}
}