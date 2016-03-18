<?php
namespace Concrete\Controller\Backend\Tree\Node;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Tree\Node\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Legacy\Loader;

class DragRequest extends UserInterface
{
    protected function getNodes()
    {
        if (!isset($this->nodes)) {
            $sourceNode = Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['sourceTreeNodeID']));
            $destNode = Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
            if (is_object($sourceNode) && is_object($destNode)) {
                $this->nodes = array($sourceNode, $destNode);
            } else {
                $this->nodes = false;
            }
        }
        return $this->nodes;
    }

    protected function canAccess()
    {
        list($sourceNode, $destNode) = $this->getNodes();
        if (is_object($sourceNode)) {
            $sp = new \Permissions($sourceNode);
            $dp = new \Permissions($destNode);
            return $dp->canAddTreeSubNode();
        }
    }

    public function execute()
    {
        list($sourceNode, $destNode) = $this->getNodes();
        if (is_object($sourceNode)) {
            $sourceNode->move($destNode);
            $destNode->saveChildOrder($_POST['treeNodeID']);
            return new JsonResponse($destNode->getTreeNodeJSON());
        }
    }
}
