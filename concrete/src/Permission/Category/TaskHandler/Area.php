<?php

namespace Concrete\Core\Permission\Category\TaskHandler;

use Concrete\Core\Area\Area as ConcreteArea;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Area extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $area = $this->getArea($options);
        if ($area === null) {
            throw new UserMessageException(t('Area not received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($area, $options);
    }

    protected function getArea(array $options): ?ConcreteArea
    {
        $page = ConcretePage::getByID($options['cID']);
        $area = ConcreteArea::get($page, $options['arHandle']);
        if (!$area) {
            return null;
        }
        $ap = new Checker($area);
        if (!$ap->canEditAreaPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        if ($area->isGlobalArea()) {
            $stack = Stack::getByName($options['arHandle']);
            $area = self::get($stack, STACKS_AREA_NAME);
            if (!$area) {
                return null;
            }
        }

        return $area;
    }

    protected function addAccessEntity(ConcreteArea $area, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($area);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
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

    protected function removeAccessEntity(ConcreteArea $area, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($area);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(ConcreteArea $area, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($area);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(ConcreteArea $area, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($area);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
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
