<?php 
namespace Concrete\Core\Http;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class HttpServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'ajax' => '\Concrete\Core\Http\Service\Ajax',
			'json' => '\Concrete\Core\Http\Service\Json'

		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}