<?php
namespace Concrete\Core\Navigation;

use Concrete\Core\Navigation\Item\ItemInterface;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class NavigationFactory
{

    /**
     * @var Page
     */
    protected $home;

    public function __construct(Page $home)
    {
        $this->home = $home;
    }

    public function createItemFromPage(Page $page): ItemInterface
    {
        return new PageItem($page);
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
        if ($permissions->canViewPage()) {
            $item = $this->createItemFromPage($currentPage);
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
        $navigation = new Navigation();
        $children = $this->home->getCollectionChildren();
        foreach($children as $child) {
            $navigation = $this->populateNavigation($child, $navigation);
        }
        return $navigation;
    }

}
