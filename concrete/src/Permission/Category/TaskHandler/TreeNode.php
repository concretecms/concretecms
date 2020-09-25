<?php

namespace Concrete\Core\Permission\Category\TaskHandler;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Tree\Node\Node;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class TreeNode extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $node = $this->getNode($options);
        if ($node === null) {
            throw new UserMessageException(t('Tree node not received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($node, $options);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Tree\Node\Node|null
     */
    protected function getNode(array $options): ?Node
    {
        $nodeID = empty($options['treeNodeID']) ? 0 : (int) $options['treeNodeID'];
        $node = $nodeID > 0 ? Node::getByID($nodeID) : null;
        if ($node === null || $node->isError()) {
            return null;
        }
        $np = new Checker($node);
        if (!$np->canEditTreeNodePermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $node;
    }

    protected function addAccessEntity(Node $node, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($node);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function revertToGlobalNodePermissions(Node $node, array $options): ?Response
    {
        $node->setTreeNodePermissionsToGlobal();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function overrideGlobalNodePermissions(Node $node, array $options): ?Response
    {
        $node->setTreeNodePermissionsToOverride();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(Node $node, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($node);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(Node $node, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($node);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(Node $node, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($node);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }

    protected function savePermissionAssignments(Node $node, array $options): ?Response
    {
        $permissions = Key::getList($node->getPermissionObjectKeyCategoryHandle());
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($node);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->clearPermissionAssignment();
            $paID = $options['pkID'][$pk->getPermissionKeyID()] ?? 0;
            if ($paID > 0) {
                $pa = Access::getByID($paID, $pk);
                if ($pa !== null) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
