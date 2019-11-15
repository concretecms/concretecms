<?php
namespace Concrete\Controller\Dialog\Tree\Node\Category;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Support\Facade\Facade;
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
        $app = Facade::getFacadeApplication();
        $token = $app->make('token');
        $error = $app->make('error');
        $vsh = $app->make('helper/validation/strings');
        $node = $this->getNode();
        if (!$token->validate('update_category_node')) {
            $error->add($token->getErrorMessage());
        }

        $title = $_POST['treeNodeCategoryName'];
        if (!$vsh->notempty($title)) {
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
