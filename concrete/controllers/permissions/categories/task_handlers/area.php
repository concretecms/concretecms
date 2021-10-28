<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Area\Area as ConcreteArea;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\ObjectInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Area extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $page = ConcretePage::getByID($options['cID']);
        $area = ConcreteArea::get($page, $options['arHandle']);
        if (!$area || $area->isError()) {
            throw new UserMessageException(t('Area not received'));
        }
        $ap = new Checker($area);
        if (!$ap->canEditAreaPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        if ($area->isGlobalArea()) {
            $stack = Stack::getByName($options['arHandle']);
            $area = ConcreteArea::get($stack, STACKS_AREA_NAME);
            if (!$area || $area->isError()) {
                throw new UserMessageException(t('Area not received'));
            }
        }

        return $area;
    }

    protected function revertToPagePermissions(ConcreteArea $area, array $options): ?Response
    {
        $area->revertToPagePermissions();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function overridePagePermissions(ConcreteArea $area, array $options): ?Response
    {
        $area->overridePagePermissions();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermissionAssignments(ConcreteArea $area, array $options): ?Response
    {
        $permissions = Key::getList('area');
        foreach ($permissions as $pk) {
            $paID = $options['pkID'][$pk->getPermissionKeyID()];
            $pk->setPermissionObject($area);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->clearPermissionAssignment();
            if ($paID > 0) {
                $pa = Access::getByID($paID, $pk);
                if (is_object($pa)) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
