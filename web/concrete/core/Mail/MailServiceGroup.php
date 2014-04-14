<?php 
namespace Concrete\Core\Mail;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class MailServiceGroup extends ServiceGroup {

	public function register() {
		$register = array(
			'mail' => '\Concrete\Core\Mail\Service'
		);

		foreach($register as $key => $value) {
			$this->locator->register($key, $value);
		}
	}


}