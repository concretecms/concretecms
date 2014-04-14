<?php
namespace Concrete\Core\Utility;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;
class UtilityServiceGroup extends ServiceGroup {


	public function register() {
		$singletons = array(
			'text' => '\Concrete\Core\Utility\Service\Text',
			'arrays' => '\Concrete\Core\Utility\Service\Arrays',
			'number' => '\Concrete\Core\Utility\Service\Number',
			'xml' => '\Concrete\Core\Utility\Service\Xml',
			'url' => '\Concrete\Core\Utility\Service\Url'

		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}
}