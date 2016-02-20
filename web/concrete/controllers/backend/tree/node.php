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

    public function load()
    {
        $node = $this->getNode();
        $selected = array();
        if (is_array($_REQUEST['treeNodeSelectedIDs'])) {
            foreach ($_REQUEST['treeNodeSelectedIDs'] as $nodeID) {
                $selected[] = intval($nodeID);
            }
        }
        $node->getTreeObject()->setRequest($_REQUEST);
        $node->populateDirectChildrenOnly();

        $r = array();
        if (count($selected) > 0) {
            foreach ($selected as $match) {
                $node->selectChildrenNodesByID($match);
            }
        }
        foreach ($node->getChildNodes() as $childnode) {
            $json = $childnode->getTreeNodeJSON();
            if ($json) {
                $r[] = $json;
            }
        }
        return new JsonResponse($r);
    }
}
