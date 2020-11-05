<?php

namespace Concrete\Controller\Dialog\Permissions\Access\Entity\Types;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\Group\GroupSetList;

defined('C5_EXECUTE') or die('Access Denied.');

class GroupSet extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/access/entity/types/group_set';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $tp = new Checker();

        return $tp->canAccessGroupSearch();
    }

    public function view()
    {
        $type = Type::getByHandle('group_set');
        $this->set('url', $type->getControllerUrl());
        $this->set('gl', new GroupSetList());
    }
}
