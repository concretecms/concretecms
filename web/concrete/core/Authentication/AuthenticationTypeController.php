<?php
namespace Concrete\Core\Authentication;
use User;
use Page;
use Loader;
use Controller;
abstract class AuthenticationTypeController extends Controller implements AuthenticationTypeControllerInterface {

	protected $authenticationType;

    abstract public function getAuthenticationTypeIconHTML();

	public function __construct(AuthenticationType $type) {
		$this->authenticationType = $type;
	}

	public function getAuthenticationType() {
		return $this->authenticationType;
	}

	public function completeAuthentication(User $u) {
		$c = Page::getByPath('/login');
		$controller = $c->getPageController();
		$controller->finishAuthentication($this->getAuthenticationType());
	}

}