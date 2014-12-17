<?php
namespace Concrete\Core\Validation;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;
class ValidationServiceProvider extends ServiceProvider {


	public function register() {
		$singletons = array(
			'helper/validation/antispam' => '\Concrete\Core\Antispam\Service',
			'helper/validation/captcha' => '\Concrete\Core\Captcha\Service',
			'helper/validation/file' => '\Concrete\Core\File\ValidationService',
			'helper/validation/form' => '\Concrete\Core\Form\Service\Validation',
			'helper/validation/identifier' => '\Concrete\Core\Utility\Service\Identifier',
			'helper/validation/ip' => '\Concrete\Core\Permission\IPService',
			'helper/validation/numbers' => '\Concrete\Core\Utility\Service\Validation\Numbers',
			'helper/validation/strings' => '\Concrete\Core\Utility\Service\Validation\Strings',
			'helper/validation/banned_words' => '\Concrete\Core\Validation\BannedWord\Service',
			'helper/security' => '\Concrete\Core\Validation\SanitizeService'

		);
		$registers = array(
			'helper/validation/token' => '\Concrete\Core\Validation\CSRF\Token',
			'helper/validation/error' => '\Concrete\Core\Error\Error',
			'token' => '\Concrete\Core\Validation\CSRF\Token'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
		foreach($registers as $key => $value) {
			$this->app->bind($key, $value);
		}
	}
}