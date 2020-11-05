<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Area\Area as ConcreteArea;
use Concrete\Core\Block\Block as ConcreteBlock;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\Group\GroupRepository;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Block extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $subject = $this->getSubject($options);
        if ($subject === null) {
            throw new UserMessageException(t('Block not received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }
        $this->configurePageVersion($subject);

        return $this->{$method}($subject, $options);
    }

    protected function getSubject(array $options): ?array
    {
        $currentPage = empty($options['cID']) ? null : ConcretePage::getByID($options['cID']);
        $area = ConcreteArea::get($currentPage, $options['arHandle']);
        if (!$area || $area->isError()) {
            return null;
        }
        $isGlobalArea = (bool) $area->isGlobalArea();
        if ($isGlobalArea) {
            $actualPage = Stack::getByName($options['arHandle']);
        } else {
            $actualPage = $currentPage;
        }
        $block = ConcreteBlock::getByID($options['bID'], $actualPage, $isGlobalArea ? STACKS_AREA_NAME : $area);
        if (!$block || $block->isError()) {
            return null;
        }

        return [
            'block' => $block,
            'currentPage' => $currentPage,
            'actualPage' => $actualPage,
            'permissions' => new Checker($block),
        ];
    }

    protected function configurePageVersion(array $subject): void
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            return;
        }
        $nvc = $subject['actualPage']->getVersionToModify();
        if ($subject['currentPage'] !== $subject['actualPage']) {
            $xvc = $subject['currentPage']->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }
        $subject['block']->loadNewCollection($nvc);
    }

    protected function addAccessEntity(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($subject['block']);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function revertToAreaPermissions(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $subject['block']->revertToAreaPermissions();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function overrideAreaPermissions(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $subject['block']->doOverrideAreaPermissions();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($subject['block']);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($subject['block']);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($subject['block']);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }

    protected function savePermissionAssignments(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canEditBlockPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $permissions = Key::getList('block');
        foreach ($permissions as $pk) {
            $paID = $options['pkID'][$pk->getPermissionKeyID()];
            $pk->setPermissionObject($subject['block']);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->clearPermissionAssignment();
            if ($paID > 0) {
                $pa = Access::getByID($paID, $pk);
                if ($pa) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function setTimedGuestAccess(array $subject, array $options): ?Response
    {
        if (!$subject['permissions']->canScheduleGuestAccess()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $b = $subject['block'];
        if (!$b->overrideAreaPermissions()) {
            $b->doOverrideAreaPermissions();
        }
        $pk = Key::getByHandle('view_block');
        $pk->setPermissionObject($b);
        $pa = $pk->getPermissionAccessObject();
        if (!$pa) {
            $pa = Access::create($pk);
        } elseif ($pa->isPermissionAccessInUse()) {
            $pa = $pa->duplicate();
        }
        $pe = GroupEntity::getOrCreate($this->app->make(GroupRepository::class)->getGroupByID(GUEST_GROUP_ID));
        $pd = Duration::createFromRequest();
        $pa->addListItem($pe, $pd, Key::ACCESS_TYPE_INCLUDE);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
