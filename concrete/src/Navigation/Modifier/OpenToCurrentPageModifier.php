<?php
namespace Concrete\Core\Navigation\Modifier;

use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Navigation\Modifier\Traits\GetPageItemFromNavigationTrait;
use Concrete\Core\Navigation\NavigationInterface;
use Concrete\Core\Page\Page;

class OpenToCurrentPageModifier implements ModifierInterface
{

    use GetPageItemFromNavigationTrait;

    /**
     * @var Page
     */
    protected $currentPage;

    /**
     * @var Navigation
     */
    protected $navigationService;

    public function __construct(Navigation $navigationService, Page $currentPage)
    {
        $this->navigationService = $navigationService;
        $this->currentPage = $currentPage;
    }

    protected function removeUnrelatedSectionsFromNavigation(array $items, array $sectionIDs)
    {
        foreach($items as $item) {
            /**
             * @var $item PageItem
             */
            if ($item->getPageID() == $this->currentPage->getCollectionID()) {
                $item->setIsActive(true);
            } else if (in_array($item->getPageID(), $sectionIDs)) {
                $item->setIsActiveParent(true);
            } else {
                $item->setChildren([]);
            }

            $this->removeUnrelatedSectionsFromNavigation($item->getChildren(), $sectionIDs);
        }
    }

    protected function getSectionIDs(): array
    {
        $parents = array_reverse($this->navigationService->getTrailToCollection($this->currentPage));
        $sectionIDs = array_map(function($page) { return $page->getCollectionID(); }, $parents);
        return $sectionIDs;
    }

    public function modify(NavigationInterface $navigation)
    {
        $this->removeUnrelatedSectionsFromNavigation($navigation->getItems(), $this->getSectionIDs());
    }

}
