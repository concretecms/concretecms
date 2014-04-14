<?php
namespace Concrete\Core\Validation;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;
class ValidationServiceGroup extends ServiceGroup {


	public function register() {
		$singletons = array(
			'validation/antispam' => '\Concrete\Core\Antispam\Service',
			'validation/captcha' => '\Concrete\Core\Captcha\Service',
			'validation/error' => '\Concrete\Core\Error\Error',
			'validation/file' => '\Concrete\Core\File\ValidationService',
			'validation/form' => '\Concrete\Core\Form\Service\Validation',
			'validation/identifier' => '\Concrete\Core\Utility\IdentifierService',
			'validation/ip' => '\Concrete\Core\Permission\IPService',
			'validation/numbers' => '\Concrete\Core\Utility\Service\Validation\Numbers',
			'validation/strings' => '\Concrete\Core\Utility\Service\Validation\Strings',
			'validation/banned_words' => '\Concrete\Core\Validation\BannedWord\Service',
			'security' => '\Concrete\Core\Validation\SanitizeService'

		);
		$registers = array(
			'validation/token' => '\Concrete\Core\Validation\CSRF\Token'
		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
		foreach($registers as $key => $value) {
			$this->locator->register($key, $value);
		}
	}
}