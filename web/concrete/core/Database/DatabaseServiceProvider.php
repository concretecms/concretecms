<?php
namespace Concrete\Core\Database;

use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

/**
 * Class DatabaseServiceProvider
 * @package Concrete\Core\Database
 */
class DatabaseServiceProvider extends ServiceProvider {
	/**
	 * Used to create a singleton instance of our database class
	 */
	public function register() {
		$this->app->singleton('database', '\Concrete\Core\Database\Database');
	}

}