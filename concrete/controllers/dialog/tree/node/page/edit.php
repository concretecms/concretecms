<?php
namespace Concrete\Controller\Dialog\Tree\Node\Page;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page as CorePage;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class Edit extends Node
{
    protected $viewPath = '/dialogs/tree/node/page/edit';

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

    public function update_page_node()
    {
        if ($this->validateAction()) {
            $error = \Core::make('error');
            $node = $this->getNode();
            $page = CorePage::getByID($this->request->request->get('pageID'));
            $checker = new Checker($page);
            if (!$checker->canViewPage()) {
                $error->add(t('Invalid page object.'));
            }


            if (!$error->has()) {
                $node->setDetails($page, $this->request->request->getBoolean('includeSubpagesInMenu'));
                $r = $node->getTreeNodeJSON();
                return new JsonResponse($r);
            } else {
                return new JsonResponse($error);
            }
        } else {
            throw new UserMessageException(t('Access Denied'));
        }
    }
}
