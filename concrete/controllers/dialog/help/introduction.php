<?php
namespace Concrete\Controller\Dialog\Help;

use Concrete\Controller\Backend\UserInterface;

/**
 * @since 5.7.4
 */
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
