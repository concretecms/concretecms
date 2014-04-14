<?php 
namespace Concrete\Core\Form;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class FormServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'form' => '\Concrete\Core\Application\Form\Service\Form',
			'form/attribute' => '\Concrete\Core\Application\Form\Service\Widget\Attribute',
			'form/color' => '\Concrete\Core\Application\Form\Service\Widget\Color',
			'form/date_time' => '\Concrete\Core\Application\Form\Service\Widget\DateTime',
			'form/page_selector' => '\Concrete\Core\Application\Form\Service\Widget\PageSelector',
			'form/rating' => '\Concrete\Core\Application\Form\Service\Widget\Rating',
			'form/user_selector' => '\Concrete\Core\Application\Form\Service\Widget\UserSelector'	


		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}