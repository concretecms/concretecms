<?php
namespace Concrete\Core\Authentication;

use Concrete\Core\User\User;
use Page;
use Controller;
use Concrete\Core\Support\Facade\Application;

abstract class AuthenticationTypeController extends Controller implements AuthenticationTypeControllerInterface
{
    protected $authenticationType;
    protected $app;

    abstract public function getAuthenticationTypeIconHTML();
    abstract public function view();

    /**
     * @param AuthenticationType $type This type may be null only for access points that do not rely on the type.
     */
    public function __construct(AuthenticationType $type = null)
    {
        $this->authenticationType = $type;
        $this->app = Application::getFacadeApplication();
    }

    public function getAuthenticationType()
    {
        if (!$this->authenticationType) {
            $this->authenticationType = AuthenticationType::getByHandle($this->getHandle());
        }

        return $this->authenticationType;
    }

    public function completeAuthentication(User $u)
    {
        $c = Page::getByPath('/login');
        $controller = $c->getPageController();
        return $controller->finishAuthentication($this->getAuthenticationType(), $u);
    }

    /**
     * @return string
     */
    abstract public function getHandle();
}
