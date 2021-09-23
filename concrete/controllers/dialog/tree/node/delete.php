<?php
namespace Concrete\Controller\Dialog\Tree\Node;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class Delete extends Node
{
    protected $viewPath = '/dialogs/tree/node/delete';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new Checker($node);
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
            if ($node->getTreeNodeTypeHandle() == 'file_folder') {
                $this->flash('success', t('File folder deleted successfully.'));
            }  else {
                // The file manager has a different way of handling this than just showing the message.
                $response->setMessage(t('%s deleted successfully.', $node->getTreeNodeDisplayName()));
            }
            $response->setAdditionalDataAttribute('treeNodeID', $treeNodeID);
            $response->setAdditionalDataAttribute('treeJSONObject', $node->getJSONObject());

            $node->delete();
            return new JsonResponse($response);
        } else {
            return new JsonResponse($error);
        }

    }

}
