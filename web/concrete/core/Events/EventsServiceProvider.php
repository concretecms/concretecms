<?php 
namespace Concrete\Core\Events;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EventsServiceProvider extends ServiceProvider {

	public function register() {

		$this->app['director'] = $this->app->share(function($app) {
			return new \Symfony\Component\EventDispatcher\EventDispatcher();
		});


	}


}