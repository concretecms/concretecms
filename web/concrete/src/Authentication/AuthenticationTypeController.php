<?php
namespace Concrete\Core\Authentication;

use Controller;
use User;

abstract class AuthenticationTypeController extends Controller implements AuthenticationTypeControllerInterface
{

    protected $authenticationType;

    abstract public function getAuthenticationTypeIconHTML();

    abstract public function view();

    public function __construct(AuthenticationType $type)
    {
        $this->authenticationType = $type;
    }

    public function getAuthenticationType()
    {
        return $this->authenticationType;
    }

    public function completeAuthentication(User $user)
    {
        \Core::make('auth')->completeAuthentication($this->getAuthenticationType(), $user);
    }

}
