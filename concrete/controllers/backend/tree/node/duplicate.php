<?php
namespace Concrete\Controller\Backend\Tree\Node;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Tree\Node\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Legacy\Loader;

class Duplicate extends UserInterface
{
    protected $node;

    protected function getNode()
    {
        if (!isset($this->node)) {
            $this->node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeID']));
        }
        return $this->node;
    }

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canDuplicateTreeNode();
    }

    public function execute()
    {
        $node = $this->getNode();
        $parent = $node->getTreeNodeParentObject();
        $new = $node->duplicate($parent);
        $r = new \stdClass();
        $r->treeNodeParentID = $parent->getTreeNodeID();
        return new JsonResponse($r);    }
}
