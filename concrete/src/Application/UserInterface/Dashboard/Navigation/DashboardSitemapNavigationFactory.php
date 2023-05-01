<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\Traits\CheckPageForInclusionInMenuTrait;
use Concrete\Core\Navigation\Item\DashboardPageItem;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class DashboardSitemapNavigationFactory
{

    use CheckPageForInclusionInMenuTrait;

    protected function getPageChildren(Page $page)
    {
        $accountChildren = null;
        if ($page->getCollectionPath() == '/dashboard/welcome') {
            $accountPage = Page::getByPath('/account');
            $accountChildren = $accountPage->getCollectionChildren();
        }
        $children = $page->getCollectionChildren();
        if (isset($accountChildren)) {
            return array_merge($children, $accountChildren);
        }
        return $children;
    }


    /**
     * @param Page $currentPage
     * @param Navigation $navigation
     * @param PageItem|null $currentItem
     * @return Navigation
     */
    protected function populateNavigation(Page $currentPage, Navigation $navigation, PageItem $currentItem = null)
    {
        if ($this->includePageInMenu($currentPage)) {
            $item = new DashboardPageItem($currentPage);
            $children = $this->getPageChildren($currentPage);
            foreach ($children as $child) {
                $this->populateNavigation($child, $navigation, $item);
            }
            if ($currentItem) {
                $currentItem->addChild($item);
            } else {
                $navigation->add($item);
            }
        }
        return $navigation;
    }

    public function createNavigation(Page $page): Navigation
    {
        $navigation = new Navigation();
        $children = $page->getCollectionChildren();
        foreach ($children as $child) {
            $navigation = $this->populateNavigation($child, $navigation);
        }
        return $navigation;
    }

}