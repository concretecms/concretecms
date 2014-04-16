<?php 
namespace Concrete\Core\Http;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class HttpServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'helper/ajax' => '\Concrete\Core\Http\Service\Ajax',
			'helper/json' => '\Concrete\Core\Http\Service\Json'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}

	}


}