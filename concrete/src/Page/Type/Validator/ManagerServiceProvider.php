<?php 
namespace Concrete\Core\Page\Type\Validator;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
	public function register()
    {
        $this->app['manager/page_type/validator'] = $this->app->share(function($app) {
            return new Manager($app);
        });
	}
}