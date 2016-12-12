<?php 
namespace Concrete\Core\Area\Layout\Preset\Provider;
use Concrete\Core\Area\Layout\Preset\Provider\UserProvider;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{

	public function register()
    {
        $this->app['manager/area_layout_preset_provider'] = $this->app->share(function($app) {
            $manager =  new Manager($app);
            $manager->register(new UserProvider());
            $manager->register(new ActiveThemeProvider());
            return $manager;
        });
	}
}