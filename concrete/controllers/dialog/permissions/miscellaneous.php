<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Permission\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

class Miscellaneous extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/miscellaneous';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $p = new Checker();

        return $p->canAccessTaskPermissions();
    }

    public function view()
    {
    }
}
