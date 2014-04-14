<?php 
namespace Concrete\Core\Form;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FormServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'form' => '\Concrete\Core\Form\Service\Form',
			'form/attribute' => '\Concrete\Core\Form\Service\Widget\Attribute',
			'form/color' => '\Concrete\Core\Form\Service\Widget\Color',
			'form/date_time' => '\Concrete\Core\Form\Service\Widget\DateTime',
			'form/page_selector' => '\Concrete\Core\Form\Service\Widget\PageSelector',
			'form/rating' => '\Concrete\Core\Form\Service\Widget\Rating',
			'form/user_selector' => '\Concrete\Core\Form\Service\Widget\UserSelector'	


		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}