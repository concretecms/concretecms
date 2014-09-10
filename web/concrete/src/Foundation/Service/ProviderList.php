<?php
namespace Concrete\Core\Foundation\Service;
use \Concrete\Core\Application\Application;

class ProviderList {

	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * Loads and registers a class ServiceProvider class.
	 * @param  string $class
	 * @return void
	 */
	public function registerProvider($class) {
		$cl = new $class($this->app);
		$cl->register();
	}

	/**
	 * Registers an array of service group classes.
	 * @param  array $groups
	 * @return void
	 */
	public function registerProviders($groups) {
		foreach($groups as $group) {
			$this->registerProvider($group);
		}
	}

	/**
	 * We are not allowed to serialize $this->app
	 */
	public function __sleep() {
		unset($this->app);
	}


}
