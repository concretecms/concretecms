<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class NavigationFactory
{

    /**
     * @var NavigationCache
     */
    protected $cache;

    public function __construct(NavigationCache $cache)
    {
        $this->cache = $cache;
    }

    protected function populateNavigation(Page $currentPage, Navigation $navigation, PageItem $currentItem = null)
    {
        $permissions = new Checker($currentPage);
        if ($permissions->canViewPage() && !$currentPage->getAttribute('exclude_nav')) {
            $item = new PageItem($currentPage);
            $children = $currentPage->getCollectionChildren();
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

    protected function getPageItemFromNavigation(Page $page, array $items)
    {
        $matchedItem = null;
        foreach($items as $item) {
            /**
             * @var $item PageItem
             */
            if ($item->getPageID() == $page->getCollectionID()) {
                $matchedItem = $item;
            } else {
                $matchedItem = $this->getPageItemFromNavigation($page, $item->getChildren());
            }
        }
        return $matchedItem;
    }

    public function createNavigation(Page $startingPage = null): Navigation
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

        if ($startingPage) {
            $startingPageItem = $this->getPageItemFromNavigation($startingPage, $navigation->getItems());
            $navigation = new Navigation();
            $navigation->setItems($startingPageItem->getChildren());
        }
        return $navigation;
    }

}
