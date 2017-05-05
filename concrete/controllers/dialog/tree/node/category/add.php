<?php
namespace Concrete\Controller\Dialog\Tree\Node\Category;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/category/add';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canAddCategoryTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
        if ($this->request->query->has('treeNodeTypeHandle')) {
            $this->set('treeNodeTypeHandle', h($this->request->query->get('treeNodeTypeHandle')));
        }
    }

    protected function getCategoryClass(Category $category)
    {
        if ($category instanceof ExpressEntryCategory) {
            return ExpressEntryCategory::class;
        }
        return Category::class;
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
            $class = false;
            if ($this->request->request->has('treeNodeTypeHandle')) {
                $type = NodeType::getByHandle($this->request->request->get('treeNodeTypeHandle'));
                if (!$type) {
                    throw new \Exception(t('Unable to get class for node type: %s', $this->request->request->get('treeNodeTypeHandle')));
                }
                $class = $type->getTreeNodeTypeClass();
            }
            if (!$class) {
                $class = Category::class;
            }
            $category = $class::add($title, $parent);
            $r = $category->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
