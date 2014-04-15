<?php 
namespace Concrete\Core\Html;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class HtmlServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'helper/html' => '\Concrete\Core\Html\Service\Html',
			'helper/overlay' => '\Concrete\Core\Html\Service\Overlay',
			'helper/navigation' => '\Concrete\Core\Html\Service\Navigation',
			'helper/pagination' => '\Concrete\Core\Html\Service\Pagination'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}