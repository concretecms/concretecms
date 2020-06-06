<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Html\Service\Navigation as NavigationService;

class NavigationFactory
{

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
