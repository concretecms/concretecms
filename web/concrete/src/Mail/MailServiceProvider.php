<?php 
namespace Concrete\Core\Mail;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class MailServiceProvider extends ServiceProvider {

	public function register() {
		$register = array(
			'helper/mail' => '\Concrete\Core\Mail\Service'
		);

		foreach($register as $key => $value) {
			$this->app->bind($key, $value);
		}
	}


}