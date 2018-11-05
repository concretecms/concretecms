<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\ColumnSet\Available;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class PageListPagerManager extends AbstractPagerManager
{

    public function getCursorStartValue($mixed)
    {
        return $mixed->getCollectionID();
    }

    public function getCursorObject($cursor)
    {
        $page = Page::getByID($cursor);
        if ($page && !$page->isError()) {
            return $page;
        }
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('p.cID', $direction);
    }



}