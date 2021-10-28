<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

class User extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/user';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $c = Page::getByPath('/dashboard/system/permissions/users');
        $cp = new Checker($c);

        return $cp->canViewPage();
    }

    public function view()
    {
    }
}
