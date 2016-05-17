<?php
namespace Concrete\Controller\Element\Navigation;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

class Menu extends ElementController
{

    protected $currentPage;
    protected $startingParentPage;
    protected $trail;
    protected $title;
    protected $wrapperClass;

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param mixed $wrapperClass
     */
    public function setWrapperClass($wrapperClass)
    {
        $this->wrapperClass = $wrapperClass;
    }

    public function __construct(Page $startingParentPage, Page $currentPage)
    {
        parent::__construct();
        $this->startingParentPage = $startingParentPage;
        $this->trail = array($currentPage->getCollectionID());
        $cParentID = Page::getCollectionParentIDFromChildID($currentPage->getCollectionID());
        while($cParentID > 0) {
            $this->trail[] = $cParentID;
            $cParentID = Page::getCollectionParentIDFromChildID($cParentID);
        }

        // pop off the dashboard node
        array_pop($this->trail);
        $this->currentPage = $currentPage;
    }

    public function getElement()
    {
        return 'navigation/menu';
    }

    public function displayChildPages(Page $page)
    {
        if ($page->getCollectionID() == $this->currentPage->getCollectionID()) {
            return true;
        }
        if (in_array($page->getCollectionID(), $this->trail)) {
            return true;
        }
    }

    public function getMenuItemClass(Page $page)
    {
        $classes = [];
        if ($page->getCollectionID() == $this->currentPage->getCollectionID()) {
            $classes[] = 'nav-selected';
        }
        if (in_array($page->getCollectionID(), $this->trail)) {
            $classes[] = 'nav-path-selected';
        }
        return implode($classes, ' ');
    }

    public function displayDivider(Page $page, Page $next = null)
    {
        return false;
    }

    public function getChildPages($parent)
    {
        $list = new PageList();
        $list->filterByExcludeNav(false);
        $list->sortByDisplayOrder();
        $list->includeSystemPages();
        $list->filterByParentID($parent->getCollectionID());
        $pages = $list->getResults();
        return $pages;
    }

    public function view()
    {
        $this->set('top', $this->getChildPages($this->startingParentPage));
        $this->set('title', $this->title);
        $this->set('wrapperClass', $this->wrapperClass);
    }
}
