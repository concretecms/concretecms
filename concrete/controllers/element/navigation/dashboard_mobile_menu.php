<?php
namespace Concrete\Controller\Element\Navigation;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

class DashboardMobileMenu extends Menu
{

    public function __construct(Page $currentPage = null)
    {
        $dashboard = \Page::getByPath('/dashboard');
        parent::__construct($dashboard, $currentPage);
    }

    public function displayChildPages(Page $page)
    {
        return true;
    }

    protected function getPageList($parent)
    {
        $list = parent::getPageList($parent);
        $list->includeSystemPages();
        return $list;
    }

    public function getElement()
    {
        return 'dashboard/mobile_menu';
    }
}
