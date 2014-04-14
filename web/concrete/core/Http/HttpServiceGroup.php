<?php 
namespace Concrete\Core\Http;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class HttpServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'ajax' => '\Concrete\Core\Http\Service\Ajax',
			'json' => '\Concrete\Core\Http\Service\Json'

		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}