<?php
namespace Concrete\Controller\Backend\Tree;

use Concrete\Controller\Backend\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Legacy\Loader;

class Node extends UserInterface
{
    protected $node;

    protected function getNode()
    {
        if (!isset($this->node)) {
            $this->node = \Concrete\Core\Tree\Node\Node::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
        }
        return $this->node;
    }

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canViewTreeNode();
    }

    protected function loadNode()
    {
        $node = $this->getNode();
        $selected = array();
        if (isset($_REQUEST['treeNodeSelectedIDs']) && is_array($_REQUEST['treeNodeSelectedIDs'])) {
            foreach ($_REQUEST['treeNodeSelectedIDs'] as $nodeID) {
                $selected[] = intval($nodeID);
            }
        }
        $node->getTreeObject()->setRequest($_REQUEST);
        $node->populateDirectChildrenOnly();

        if (count($selected) > 0) {
            foreach ($selected as $match) {
                $node->selectChildrenNodesByID($match, true);
            }
        }
        return $node;
    }

    /**
     * This endpoint is meant to be called when we are starting from a particular spot
     * in the tree. It will include the current node, and the children, while normally
     * loading the node will only return the children.
     * @return JsonResponse
     */
    public function load_starting()
    {
        $node = $this->loadNode();

        $r = array();
        foreach ($node->getChildNodes() as $childnode) {
            $json = $childnode->getTreeNodeJSON();
            if ($json) {
                $r[] = $json;
            }
        }
        $node = $node->getTreeNodeJSON();
        $node->children = $r;
        return new JsonResponse([$node]);
    }

    /**
     * Returns the child nodes of the current node.
     * @return JsonResponse
     */
    public function load()
    {
        $node = $this->loadNode();
        $r = array();
        foreach ($node->getChildNodes() as $childnode) {
            $json = $childnode->getTreeNodeJSON();
            if ($json) {
                $r[] = $json;
            }
        }
        return new JsonResponse($r);
    }
}
