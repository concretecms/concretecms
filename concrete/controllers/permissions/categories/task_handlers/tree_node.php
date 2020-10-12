<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Tree\Node\Node;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class TreeNode extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $nodeID = $options['treeNodeID'] ?? null;
        $node = $nodeID ? Node::getByID($nodeID) : null;
        if ($node === null || $node->isError()) {
            throw new UserMessageException(t('Tree node not received'));
        }
        $np = new Checker($node);
        if (!$np->canEditTreeNodePermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $node;
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
