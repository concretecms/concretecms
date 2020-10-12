<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class PageType extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $this->checkAccess();
        $pageType = $this->getPageType($options);
        if ($pageType === null) {
            throw new UserMessageException(t('Page type not found.'));
        }

        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($pageType, $options);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkAccess(): void
    {
        $p = ConcretePage::getByPath('/dashboard/pages/types');
        if ($p && !$p->isError()) {
            $cp = new Checker($p);
            if ($cp->canViewPage()) {
                return;
            }
        }
        throw new UserMessageException(t('Access Denied.'));
    }

    protected function getPageType(array $options): ?Type
    {
        $pageTypeID = (int) ($options['ptID'] ?? '');

        return $pageTypeID === 0 ? null : Type::getByID($pageTypeID);
    }

    protected function addAccessEntity(Type $pageType, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($pageType);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(Type $pageType, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($pageType);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(Type $pageType, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($pageType);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(Type $pageType, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($pageType);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }
}
