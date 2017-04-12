<?php
namespace Concrete\Controller\Element\Navigation;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

class DashboardMenu extends Menu
{

    public function __construct(Page $currentPage)
    {
        $dashboard = \Page::getByPath('/dashboard');
        parent::__construct($dashboard, $currentPage);
    }

    public function displayChildPages(Page $page)
    {
        $display = parent::displayChildPages($page);
        if ($display) {
            return true;
        } else {
            if (
                strpos($this->currentPage->getCollectionPath(), '/account') === 0 &&
                (
                    strpos($page->getCollectionPath(), '/dashboard/welcome') === 0 ||
                    strpos($page->getCollectionPath(), '/account') === 0
                )
            ) {
                return true;
            }
        }
    }

    protected function getPageList($parent)
    {
        $list = parent::getPageList($parent);
        $list->includeSystemPages();
        return $list;
    }

    public function getMenuItemClass(Page $page)
    {
        $class = parent::getMenuItemClass($page);
        $classes = explode(' ', $class);
        if (
            strpos($this->currentPage->getCollectionPath(), '/account') === 0 &&
            (
                strpos($page->getCollectionPath(), '/dashboard/welcome') === 0 ||
                strpos($page->getCollectionPath(), '/account') === 0
            )
        ) {
            $classes[] = 'nav-path-selected';
        }
        return implode(' ' , $classes);
    }

    public function getChildPages($parent)
    {
        $pages = parent::getChildPages($parent);
        if ($parent->getCollectionPath() == '/dashboard/welcome') {
            // Add My Account to the List
            $pages[] = Page::getByPath('/account');
            $site = \Core::make("site")->getSite();
            $config = $site->getConfigRepository();
            if (is_object($site) && $config->get('user.profiles_enabled')) {
                $pages[] = Page::getByPath('/members/profile');
            }
        }
        return $pages;
    }
}
