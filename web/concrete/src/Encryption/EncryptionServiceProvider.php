<?php 
namespace Concrete\Core\Encryption;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EncryptionServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'helper/encryption' => '\Concrete\Core\Encryption\EncryptionService'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}