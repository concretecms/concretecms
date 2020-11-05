<?php
namespace Concrete\Controller\Element\Navigation;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

class AccountMenu extends Menu
{

    public function __construct(Page $currentPage)
    {
        $dashboard = \Page::getByPath('/account');
        $this->setTitle(t('My Account'));
        $this->setWrapperClass('ccm-nav-wrapper');
        parent::__construct($dashboard, $currentPage);
    }

    public function displayChildPages(Page $page)
    {
        $display = parent::displayChildPages($page);
        if ($display) {
            return true;
        } else {
            if (
                strpos($this->currentPage->getCollectionPath(), '/account/welcome') === 0
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

    public function getChildPages($parent)
    {
        $pages = array();
        if ($parent->getCollectionPath() == '/account') {
            // Add My Account to the List
            $pages[] = Page::getByPath('/account/welcome');
        }
        $pages = array_merge($pages, parent::getChildPages($parent));
        return $pages;
    }
}
