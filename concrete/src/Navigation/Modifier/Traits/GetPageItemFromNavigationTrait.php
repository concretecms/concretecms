<?php
namespace Concrete\Core\Navigation\Modifier\Traits;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;

trait GetPageItemFromNavigationTrait
{

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
                return $item;
            } else {
                $item = $this->getPageItemFromNavigation($page, $item->getChildren());
                if ($item) {
                    return $item;
                }
            }
        }
    }


}
