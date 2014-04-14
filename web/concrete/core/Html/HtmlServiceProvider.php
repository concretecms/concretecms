<?php 
namespace Concrete\Core\Html;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class HtmlServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'html' => '\Concrete\Core\Html\Service\Html',
			'overlay' => '\Concrete\Core\Html\Service\Overlay',
			'navigation' => '\Concrete\Core\Html\Service\Navigation',
			'pagination' => '\Concrete\Core\Html\Service\Pagination'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}