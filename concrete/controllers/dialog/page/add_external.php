<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\Type\Type;
use URL;
use Concrete\Core\Page\EditResponse;

class AddExternal extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/add_external';

    protected function canAccess()
    {
        return $this->permissions->canAddExternalLink();
    }

    public function view()
    {
        $typesSelect = [];
        if (is_object($tree)) {
            $type = $tree->getSiteType();
            $typeList = Type::getList(false, $type);
            foreach ($typeList as $_pagetype) {
                $typesSelect[$_pagetype->getPageTypeID()] = $_pagetype->getPageTypeDisplayName();
            }
        }
        $tree = $this->page->getSiteTreeObject();

        $this->set('types', $typesSelect);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $request = \Request::getInstance();
            $this->page->addCollectionAliasExternal(
                $request->request->get('name'),
                $request->request->get('link'),
                $request->request->get('openInNewWindow'),
                $request->request->get('ptID')
            );

            $r = new EditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(URL::to('/dashboard/sitemap'));
            $r->outputJSON();
        }
    }
}
