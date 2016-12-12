<?php
namespace Concrete\Controller\Dialog\Tree\Node\Category;

use Concrete\Controller\Dialog\Tree\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

class Edit extends Node
{
    protected $viewPath = '/dialogs/tree/node/category/edit';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canEditTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }

    public function update_category_node()
    {
        $token = \Core::make('token');
        $error = \Core::make('error');
        $node = $this->getNode();
        if (!$token->validate('update_category_node')) {
            $error->add($token->getErrorMessage());
        }

        $title = $_POST['treeNodeCategoryName'];
        if (!$title) {
            $error->add(t('Invalid title for category'));
        }

        if (!$error->has()) {
            $node->setTreeNodeName($title);
            $r = $node->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
