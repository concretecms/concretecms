<?php
namespace Concrete\Core\Utility;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;
class UtilityServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
            'helper/text' => '\Concrete\Core\Utility\Service\Text',
            'helper/html' => '\Concrete\Core\Utility\Service\Html',
			'helper/arrays' => '\Concrete\Core\Utility\Service\Arrays',
			'helper/number' => '\Concrete\Core\Utility\Service\Number',
			'helper/xml' => '\Concrete\Core\Utility\Service\Xml',
			'helper/url' => '\Concrete\Core\Utility\Service\Url'

		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}
}