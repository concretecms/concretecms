<?php

namespace Concrete\Controller\Dialog\Permissions\Access\Entity\Types;

use Concrete\Controller\Backend\UserInterface;
use \Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class GroupCombination extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/access/entity/types/group_combination';

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
        $type = Type::getByHandle('group_combination');
        $this->set('url', $type->getControllerUrl());
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('dt', $this->app->make(DateTime::class));
    }
}
