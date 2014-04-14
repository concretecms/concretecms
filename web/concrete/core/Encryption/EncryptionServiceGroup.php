<?php 
namespace Concrete\Core\Encryption;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class EncryptionServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'encryption' => '\Concrete\Core\Encryption\EncryptionService'
		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}