<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\User\User;

class Introduction extends UserInterface
{
    protected $viewPath = '/dialogs/help/introduction';

    public function view()
    {
    }

    public function canAccess()
    {
        $u = $this->app->make(User::class);

        return $u->isRegistered();
    }
}
