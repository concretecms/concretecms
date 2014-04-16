<?php 
namespace Concrete\Core\Database;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider {

	public function register() {
		$this->app->singleton('database', '\Concrete\Core\Database\Database');
	}


}