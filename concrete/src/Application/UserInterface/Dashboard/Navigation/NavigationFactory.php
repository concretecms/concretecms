<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Html\Service\Navigation as NavigationService;

class NavigationFactory
{

    /**
     * @var NavigationCache
     */
    protected $cache;

    /**
     * @var NavigationService
     */
    protected $navigationService;

    public function __construct(NavigationCache $cache, NavigationService $navigationService)
    {
        $this->cache = $cache;
        $this->navigationService = $navigationService;
    }

    /**
     * @return Navigation
     */
    protected function getFullNavigation(): Navigation
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
        return clone $navigation;
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

    /**
     * @param Page $page
     * @param array $items
     * @return PageItem|mixed|null
     */
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

    /**
     * Returns an entire dashboard navigation tree. Optionally starts at a particular section in the tree.
     * Used on the Dashboard home, intelligent search, mobile menu and more.
     *
     * @param Page|null $startingPage
     * @return Navigation
     */
    public function createFullNavigation(Page $startingPage = null): Navigation
    {
        $navigation = $this->getFullNavigation();
        if ($startingPage) {
            $startingPageItem = $this->getPageItemFromNavigation($startingPage, $navigation->getItems());
            $navigation = new Navigation();
            $navigation->setItems($startingPageItem->getChildren());
        }
        return $navigation;
    }

    /**
     * Returns just the top level navigation of the dashboard. Used in the dashboard panel.
     *
     * @return Navigation
     */
    public function createTopLevelNavigation(): Navigation
    {
        $navigation = new Navigation();
        foreach($this->getFullNavigation()->getItems() as $topLevelItem) {
            $newItem = clone $topLevelItem;
            $newItem->setChildren([]);
            $navigation->add($newItem);
        }
        return $navigation;
    }

    /**
     * Returns a section of the dashboard navigation down to the current page. Used in the dashboard panel.
     *
     * @param Page $currentPage
     * @return Navigation
     */
    public function createSectionNavigation(Page $dashboardPage): ?Navigation
    {
        $removeUnrelatedSectionsFromNavigation = function($items, array $sectionIDs)
            use (&$removeUnrelatedSectionsFromNavigation) {
            foreach($items as $item) {
                /**
                 * @var $item PageItem
                 */
                if (!in_array($item->getPageID(), $sectionIDs)) {
                    $item->setChildren([]);
                }
                $removeUnrelatedSectionsFromNavigation($item->getChildren(), $sectionIDs);
            }
        };

        $parents = array_reverse($this->navigationService->getTrailToCollection($dashboardPage));
        $sectionIDs = array_map(function($page) { return $page->getCollectionID(); }, $parents);
        // Add the current page as well
        $sectionIDs[] = $dashboardPage->getCollectionID();

        // Since the dashboard starts at 0, we look at 1.
        if (isset($parents[1]) && $parents[1] instanceof Page) {
            $sectionNavigation = $this->createFullNavigation($parents[1]);
            $removeUnrelatedSectionsFromNavigation($sectionNavigation->getItems(), $sectionIDs);
            return $sectionNavigation;
        }
        return null;
    }

}
