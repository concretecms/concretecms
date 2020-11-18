<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Key\PageKey;
use Concrete\Core\Permission\Set as PermissionSet;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Request\ChangePagePermissionsInheritanceRequest as ChangePagePermissionsInheritancePageWorkflowRequest;
use Concrete\Core\Workflow\Request\ChangePagePermissionsRequest as ChangePagePermissionsPageWorkflowRequest;
use Concrete\Core\Workflow\Request\ChangeSubpageDefaultsInheritanceRequest as ChangeSubpageDefaultsInheritancePageWorkflowRequest;
use Concrete\Core\Workflow\Workflow;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Page extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $pages = $this->getPages($options);
        if ($pages === []) {
            throw new UserMessageException(t('No pages received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($pages, $options);
    }

    /**
     * @return \Concrete\Core\Page\Page[]
     */
    protected function getPages(array $options): array
    {
        $pages = [];
        foreach ((array) ($options['cID'] ?? []) as $cID) {
            $cID = (int) $cID;
            $page = $cID > 0 ? ConcretePage::getByID($cID) : null;
            if (!$page || $page->isError()) {
                continue;
            }
            $cp = new Checker($page);
            if (!$cp->canEditPagePermissions()) {
                continue;
            }
            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function addAccessEntity(array $pages, array $options): ?Response
    {
        $pk = PageKey::getByID($options['pkID']);
        $pe = PermissionAccessEntity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        foreach ($pages as $c) {
            $pk->setPermissionObject($c);
            if (isset($options['paID'])) {
                $pa = Access::getByID($options['paID'], $pk);
            } else {
                $pa = Access::getByID($pk->getPermissionAccessID(), $pk);
            }
            if (isset($options['replace']) && $options['replace']) {
                $listItems = $pa->getAccessListItems(Key::ACCESS_TYPE_ALL);
                foreach ($listItems as $listItem) {
                    $pa->removeListItem($listItem->getAccessEntityObject());
                }
            }
            $pa->addListItem($pe, $pd, $options['accessType']);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function removeAccessEntity(array $pages, array $options): ?Response
    {
        $pk = PageKey::getByID($options['pkID']);
        $pe = PermissionAccessEntity::getByID($options['peID']);
        foreach ($pages as $c) {
            $pk->setPermissionObject($c);
            $pa = Access::getByID($options['paID'], $pk);
            $pa->removeListItem($pe);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function savePermission(array $pages, array $options): ?Response
    {
        $pk = PageKey::getByID($options['pkID']);
        $pa = empty($options['paID']) ? null : Access::getByID($options['paID'], $pk);
        if ($pa !== null) {
            $pa->save($options);
            $pa->clearWorkflows();
            if (is_array($options['wfID'] ?? null)) {
                foreach ($options['wfID'] as $wfID) {
                    $wf = Workflow::getByID($wfID);
                    if ($wf !== null) {
                        $pa->attachWorkflow($wf);
                    }
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function changePermissionInheritance(array $pages, array $options): ?Response
    {
        $deferred = false;
        $homeCID = ConcretePage::getHomePageID();
        $u = $this->app->make(ConcreteUser::class);
        foreach ($pages as $c) {
            if ($c->getCollectionID() == $homeCID) {
                continue;
            }
            $pkr = new ChangePagePermissionsInheritancePageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $pkr->setPagePermissionsInheritance($options['mode'] ?? '');
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if (!($response instanceof WorkflowProgressResponse)) {
                $deferred = true;
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'deferred' => $deferred,
        ]);
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function changeSubpageDefaultsInheritance(array $pages, array $options): ?Response
    {
        $deferred = false;
        $u = $this->app->make(ConcreteUser::class);
        foreach ($pages as $c) {
            $pkr = new ChangeSubpageDefaultsInheritancePageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $pkr->setPagePermissionsInheritance($options['inherit'] ?? null);
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if (!($response instanceof WorkflowProgressResponse)) {
                $deferred = true;
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'deferred' => $deferred,
        ]);
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function displayAccessCell(array $pages, array $options): ?Response
    {
        if (count($pages) !== 1) {
            throw new UserMessageException(t('Invalid parameter received: %s', 'cID'));
        }
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($pages[0]);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     */
    protected function savePermissionAssignments(array $pages, array $options): ?Response
    {
        $permissions = Key::getList('page');
        $deferred = false;
        $u = $this->app->make(ConcreteUser::class);
        foreach ($pages as $c) {
            $pkr = new ChangePagePermissionsPageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $ps = new PermissionSet();
            $ps->setPermissionKeyCategory('page');
            foreach ($permissions as $pk) {
                $paID = $options['pkID'][$pk->getPermissionKeyID()];
                $ps->addPermissionAssignment($pk->getPermissionKeyID(), $paID);
            }
            $pkr->setPagePermissionSet($ps);
            $pkr->setRequesterUserID($u->getUserID());
            $u->unloadCollectionEdit($c);
            $response = $pkr->trigger();
            if (!($response instanceof WorkflowProgressResponse)) {
                $deferred = true;
            }
        }

        $r = new EditResponse();
        $r->setPage($c);
        if ($deferred) {
            $r->setMessage(t('Page permissions request saved successfully. You must approve this workflow request before the permissions are changed.'));
        } else {
            $r->setMessage(t('Page permissions saved successfully.'));
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($r);
    }
}
