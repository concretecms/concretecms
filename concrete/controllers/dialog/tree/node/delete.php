<?php
namespace Concrete\Controller\Dialog\Tree\Node;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Application\EditResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class Delete extends Node
{
    protected $viewPath = '/dialogs/tree/node/delete';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canDeleteTreeNode();
    }

    public function remove_tree_node()
    {
        $node = $this->getNode();
        $tree = $node->getTreeObject();
        $treeNodeID = $node->getTreeNodeID();
        $error = \Core::make('error');
        if (!\Core::make('token')->validate("remove_tree_node")) {
            $error->add(\Core::make('token')->getErrorMessage());
        }
        if (!is_object($node)) {
            $error->add(t('Invalid node.'));
        }

        if ($node->getTreeNodeParentID() == 0) {
            $error->add(t('You may not remove the top level node.'));
        }

        if (!$error->has()) {
            $response = new EditResponse();
            $response->setMessage(t('%s deleted successfully.', $node->getTreeNodeDisplayName()));
            $response->setAdditionalDataAttribute('treeNodeID', $treeNodeID);

            $node->delete();
            return new JsonResponse($response);
        } else {
            return new JsonResponse($error);
        }

    }

}
