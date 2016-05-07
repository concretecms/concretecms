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
        $sourceNodes = array();
        if (!isset($this->nodes)) {
            if (isset($_REQUEST['sourceTreeNodeID'])) {
                $sourceNode = Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['sourceTreeNodeID']));
                if (is_object($sourceNode)) {
                    $sourceNodes[] = $sourceNode;
                }
            } else if (isset($_REQUEST['sourceTreeNodeIDs'])) {
                foreach($_REQUEST['sourceTreeNodeIDs'] as $sourceTreeNodeID) {
                    $sourceNode = Node::getByID(Loader::helper('security')->sanitizeInt($sourceTreeNodeID));
                    if (is_object($sourceNode)) {
                        $sourceNodes[] = $sourceNode;
                    }
                }
            }
            $destNode = Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
            if (is_array($sourceNodes) && count($sourceNodes) && is_object($destNode)) {
                $this->nodes = array($sourceNodes, $destNode);
            } else {
                $this->nodes = false;
            }
        }
        return $this->nodes;
    }

    protected function canAccess()
    {
        list($sourceNodes, $destNode) = $this->getNodes();
        if (is_object($destNode)) {
            $dp = new \Permissions($destNode);
            return $dp->canAddTreeSubNode();
        }
    }

    public function execute()
    {
        list($sourceNodes, $destNode) = $this->getNodes();
        if (is_array($sourceNodes)) {
            foreach($sourceNodes as $sourceNode) {
                if ($_REQUEST['copyNodes']) {
                    $sourceNode->move($destNode);
                } else {
                    $sourceNode->duplicate($destNode);
                }
            }
        }

        if (isset($_POST['treeNodeID'])) {
            $destNode->saveChildOrder($_POST['treeNodeID']);
        }

        return new JsonResponse($destNode->getTreeNodeJSON());
    }
}
