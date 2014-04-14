<?php 
namespace Concrete\Core\Html;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class HtmlServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'html' => '\Concrete\Core\Html\Service\Html',
			'overlay' => '\Concrete\Core\Html\Service\Overlay',
			'navigation' => '\Concrete\Core\Html\Service\Navigation',
			'pagination' => '\Concrete\Core\Html\Service\Pagination'
		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}