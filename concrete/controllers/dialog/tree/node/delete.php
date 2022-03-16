<?php
namespace Concrete\Controller\Dialog\Tree\Node;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Tree\Node\Node as NodeObject;

class Delete extends Node
{
    protected $viewPath = '/dialogs/tree/node/delete';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new Checker($node);
        return $np->canDeleteTreeNode();
    }

    protected function validateRequest(): array
    {
        $node = $this->getNode();
        $error = new ErrorList();
        if (!\Core::make('token')->validate("remove_tree_node")) {
            $error->add(\Core::make('token')->getErrorMessage());
        }
        if (!is_object($node)) {
            $error->add(t('Invalid node.'));
        }

        if ($node->getTreeNodeParentID() == 0) {
            $error->add(t('You may not remove the top level node.'));
        }
        return [$error, $node];
    }

    protected function deleteNode(NodeObject $node)
    {
        $treeNodeID = $node->getTreeNodeID();
        $response = new EditResponse();
        $response->setMessage(t('%s deleted successfully.', $node->getTreeNodeDisplayName()));
        $response->setAdditionalDataAttribute('treeNodeID', $treeNodeID);
        $response->setAdditionalDataAttribute('treeJSONObject', $node->getJSONObject());

        $node->delete();
        return new JsonResponse($response);
    }

    public function remove_tree_node()
    {
        list($error, $node) = $this->validateRequest();
        if (!$error->has()) {
            return $this->deleteNode($node);
        } else {
            return new JsonResponse($error);
        }

    }

}
