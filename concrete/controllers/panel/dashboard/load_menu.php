<?php
namespace Concrete\Controller\Panel\Dashboard;

use Concrete\Controller\Backend\UserInterface\Page as BackendUIPageController;
use Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

class LoadMenu extends BackendUIPageController
{
    protected $viewPath = '/panels/dashboard/load_menu';

    public function canAccess()
    {
        return $this->permissions->canEditPageTemplate() || $this->permissions->canEditPageTheme();
    }

    public function view()
    {
        $click = Request::request('loadMenu');
        $page = Page::getByID($click);
        $name = $page->getCollectionName();
        $link = $page->getCollectionLink();
        $id = $page->getCollectionID();

        $parentID = $page->getCollectionParentID();
        $list = new PageList();
        $list->filterByExcludeNav(false);
        $list->sortByDisplayOrder();
        $list->filterByParentID($id);
        $list->includeSystemPages();
        $pages = $list->getResults();
        if (!$pages) {
            $pages = 'none';
        }

        $parentID = $page->getCollectionParentID();
        $parent = Page::getByID($parentID);
        $parentTitle = $parent->getCollectionName();

        $id = $page->getCollectionID();

        $data = [];
        $data['id'] = $click;
        $data['pageID'] = $id;
        $data['name'] = $name;
        $data['link'] = $link;
        $data['parentID'] = $parentID;
        $data['title'] = $parentTitle;
        $data['children'] = $pages;

        $this->set('data', $data);
    }
}
