<?php
namespace Concrete\Controller\Dialog\Tree\Node\Page;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Page\Page as CorePage;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\Page as PageNode;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/page/add';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canAddTopicTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }

    public function add_page_node()
    {
        if ($this->validateAction()) {

            $error = \Core::make('error');
            $parent = $this->getNode();

            if (!is_object($parent)) {
                $error->add(t('Invalid parent category'));
            }

            $page = CorePage::getByID($this->request->request->get('pageID'));
            $checker = new Checker($page);
            if (!$checker->canViewPage()) {
                $error->add(t('Invalid page object.'));
            }
            if (!$error->has()) {
                $pageNode = PageNode::add($page, $this->request->request->getBoolean('includeSubpagesInMenu'), $parent);
                $r = $pageNode->getTreeNodeJSON();
                return new JsonResponse($r);
            } else {
                return new JsonResponse($error);
            }
        }
    }
}
