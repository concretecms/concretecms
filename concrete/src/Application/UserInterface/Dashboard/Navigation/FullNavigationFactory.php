<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class FullNavigationFactory
{

    /**
     * @var NavigationCache
     */
    protected $cache;

    public function __construct(NavigationCache $cache)
    {
        $this->cache = $cache;
    }

    protected function getPageChildren(Page $page)
    {
        if ($page->getCollectionPath() == '/dashboard/welcome') {
            $page = Page::getByPath('/account');
        }
        $children = $page->getCollectionChildren();
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
        $permissions = new Checker($currentPage);
        if ($permissions->canViewPage() && !$currentPage->getAttribute('exclude_nav')) {
            $item = new PageItem($currentPage);
            $children = $this->getPageChildren($currentPage);
            foreach($children as $child) {
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

    /**
     * Returns an entire dashboard navigation tree. Optionally starts at a particular section in the tree.
     * Used on the Dashboard home, intelligent search, mobile menu and more.
     *
     * @return Navigation
     */
    public function createNavigation(): Navigation
    {
        if (!$this->cache->has()) {
            $navigation = new Navigation();
            $home = Page::getByPath('/dashboard');
            $children = $home->getCollectionChildren();
            foreach($children as $child) {
                $navigation = $this->populateNavigation($child, $navigation);
            }
            $this->cache->set($navigation);
        } else {
            $navigation = $this->cache->get();
        }
        return $navigation;
    }

}
