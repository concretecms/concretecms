<?php
namespace Concrete\Core\Authentication;

use Concrete\Core\Application\Application;
use Concrete\Core\Authentication\Events\Authentication;

class Authenticator
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function authenticate(AuthenticationType $type)
    {
        $type->controller->authenticate();
    }

    public function completeAuthentication(AuthenticationType $type, \User $user)
    {
        $event = new Authentication($type, $user);
        \Events::dispatch('on_authentication_complete', $event);
    }

}
