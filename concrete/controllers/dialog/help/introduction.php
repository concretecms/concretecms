<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;

class Introduction extends UserInterface
{

    protected $viewPath = '/dialogs/help/introduction';

    public function view()
    {
    }

    public function canAccess()
    {
        $u = new \User();
        return $u->isRegistered();
    }

}
