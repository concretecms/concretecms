<?php
namespace Concrete\Core\Authentication;
use User;
use Page;
use Loader;
use Controller;
abstract class AuthenticationTypeController extends Controller implements AuthenticationTypeControllerInterface {

	protected $authenticationType;

    abstract public function getAuthenticationTypeIconHTML();
    abstract public function view();

    /**
     * @param AuthenticationType $type This type may be null only for access points that do not rely on the type.
     */
	public function __construct(AuthenticationType $type=null) {
		$this->authenticationType = $type;
	}

	public function getAuthenticationType() {
        if (!$this->authenticationType) {
            $this->authenticationType = \AuthenticationType::getByHandle($this->getHandle());
        }
		return $this->authenticationType;
	}

	public function completeAuthentication(User $u) {
		$c = Page::getByPath('/login');
		$controller = $c->getPageController();
		$controller->finishAuthentication($this->getAuthenticationType());
	}

    /**
     * @return string
     */
    abstract function getHandle();

}
