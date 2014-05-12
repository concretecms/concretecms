<?php 
namespace Concrete\Core\Logging;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class LoggingServiceProvider extends ServiceProvider {

	public function register() {
        $logger = new Logger();
        $this->app->instance('log', $logger);
	}


}