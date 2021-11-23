<?php

namespace Concrete\Controller\Dialog\Permissions\Tree;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Utility\Service\Validation\Numbers;

defined('C5_EXECUTE') or die('Access Denied.');

class Node extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/tree/node';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $np = new Checker($this->getNode());

        return $np->canEditTreeNodePermissions();
    }

    public function view()
    {
        $this->set('node', $this->getNode());
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getNode(): TreeNode
    {
        $nodeID = $this->request->request->get('treeNodeID', $this->request->query->get('treeNodeID'));
        $nodeID = $this->app->make(Numbers::class)->integer($nodeID, 1) ? (int) $nodeID : null;
        $node = $nodeID === null ? null : TreeNode::getByID($nodeID);
        if ($node === null) {
            throw new UserMessageException(t('Tree node not found'));
        }

        return $node;
    }
}
