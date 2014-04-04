<?php

namespace Concrete\Core\Authentication;
use User;
abstract class AuthenticationTypeController extends \Concrete\Core\Controller\Controller implements AuthenticationTypeControllerInterface {

	protected $authenticationType;

	public function __construct(AuthenticationType $type) {
		$this->authenticationType = $type;
	}

	public function getAuthenticationType() {
		return $this->authenticationType;
	}

	public function completeAuthentication(User $u) {
		Loader::controller('/login')->finishAuthentication($this->getAuthenticationType());
	}

}