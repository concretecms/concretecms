<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\Item;
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

    protected function populateNavigation(Page $currentPage, Navigation $navigation, Item $currentItem = null)
    {
        $permissions = new Checker($currentPage);
        if ($permissions->canViewPage() && !$currentPage->getAttribute('exclude_nav')) {
            $item = new Item($currentPage->getCollectionLink(), $currentPage->getCollectionName());
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
