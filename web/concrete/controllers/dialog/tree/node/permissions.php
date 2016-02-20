<?php
namespace Concrete\Controller\Dialog\Tree\Node;

use Concrete\Controller\Backend\UserInterface;

class Permissions extends UserInterface
{
    protected $viewPath = '/dialogs/tree/node/permissions';

    protected function getNode()
    {
        if (!isset($this->node)) {
            $this->node = \Concrete\Core\Tree\Node\Node::getByID(\Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
        }
        return $this->node;
    }

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canEditTreeNodePermissions();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }
}
