<?php
namespace Concrete\Controller\Dialog\Tree\Node\TopicCategory;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Tree\Node\Type\TopicCategory;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/topic_category/add';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canAddTopicCategoryTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }

    public function add_category_node()
    {
        $token = \Core::make('token');
        $error = \Core::make('error');
        $parent = $this->getNode();
        if (!$token->validate('add_category_node')) {
            $error->add($token->getErrorMessage());
        }


        $title = $_POST['treeNodeCategoryName'];
        if (!$title) {
            $error->add(t('Invalid title for category'));
        }

        if (!is_object($parent)) {
            $error->add(t('Invalid parent category'));
        }

        if (!$error->has()) {
            $category = TopicCategory::add($title, $parent);
            $r = $category->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
