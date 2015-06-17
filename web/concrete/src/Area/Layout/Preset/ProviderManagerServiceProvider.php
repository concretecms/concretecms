<?php 
namespace Concrete\Core\Area\Layout\Preset;
use Concrete\Core\Area\Layout\Preset\Provider\UserProvider;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ProviderManagerServiceProvider extends ServiceProvider
{

	public function register()
    {
        $this->app['manager/area_layout_preset_provider'] = $this->app->share(function($app) {
            $manager =  new ProviderManager($app);
            $manager->register(new UserProvider());
            return $manager;
        });
	}
}