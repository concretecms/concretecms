<?php
namespace Concrete\Controller\Element\Navigation;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

/**
 * Note: this is an internal class with weird internal behaviors. If you want to use an actually maintained,
 * simpler version of this class styled explicitly for bootstrap 4, use the menu class instead.
 *
 * Class ApplicationMenu
 */
class ApplicationMenu extends ElementController
{
    protected $currentPage;
    protected $startingParentPage;
    protected $trail = [];
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

    public function __construct(Page $startingParentPage, Page $currentPage = null)
    {
        parent::__construct();
        $this->startingParentPage = $startingParentPage;
        if (is_object($currentPage)) {
            $this->trail = [$currentPage->getCollectionID()];
            $cParentID = Page::getCollectionParentIDFromChildID($currentPage->getCollectionID());
            while ($cParentID > 0) {
                $this->trail[] = $cParentID;
                $cParentID = Page::getCollectionParentIDFromChildID($cParentID);
            }
        }

        // pop off the dashboard node
        array_pop($this->trail);
        $this->currentPage = $currentPage;
    }

    public function getElement()
    {
        return 'navigation/application_menu';
    }

    public function displayChildPages(Page $page)
    {
        if (!is_object($this->currentPage)) {
            return false;
        }
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
        if (is_object($this->currentPage) && $page->getCollectionID() == $this->currentPage->getCollectionID()) {
            $classes[] = 'nav-selected';
            $classes[] = 'active';
        }
        if (in_array($page->getCollectionID(), $this->trail)) {
            $classes[] = 'nav-path-selected';
        }

        return implode(' ', $classes);
    }

    public function displayDivider(Page $page, Page $next = null)
    {
        return false;
    }

    protected function getPageList($parent)
    {
        $list = new PageList();
        $list->filterByExcludeNav(false);
        $list->sortByDisplayOrder();
        $list->filterByParentID($parent->getCollectionID());

        return $list;
    }

    public function getChildPages($parent)
    {
        $list = $this->getPageList($parent);
        $pages = $list->getResults();

        return $pages;
    }

    public function view()
    {
        $pages = $this->getChildPages($this->startingParentPage);
        $this->set('top', $pages);
        $this->set('title', $this->title);
        $this->set('wrapperClass', $this->wrapperClass);
    }
}
