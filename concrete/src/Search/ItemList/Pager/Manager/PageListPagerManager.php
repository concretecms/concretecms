<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Search\ItemList\Column;
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

    public function sortListByCursor(PagerProviderInterface $itemList)
    {
        $itemList->getQueryObject()->addOrderBy('p.cID', 'asc');
    }

    public function filterQueryAtOffset(PagerProviderInterface $itemList, Column $column, $sort, $mixed)
    {
        $query = $itemList->getQueryObject();
        $where = sprintf('%s %s :offset', $column->getKey(), $sort);
        switch ($column->getKey()) {
            case 'cv.cvDatePublic':
                $offset = $mixed->getCollectionDatePublic();
                break;
            case 'p.cDisplayOrder':
                $offset = $mixed->getCollectionDisplayOrder();
                break;
            case 'c.cDateModified':
                $offset = $mixed->getCollectionDateLastModified();
                break;
            case 'cv.cvName':
                $where = sprintf('(cv.cvName, p.cID) %s (:sortName, :sortID)', $sort);
                $query->setParameter('sortName', $mixed->getCollectionName());
                $query->setParameter('sortID', $mixed->getCollectionID());
                break;
            default:
                $handle = substr($column->getKey(), 3);
                $where = sprintf('(' . $column->getKey() . ', p.cID) %s (:sortName, :sortID)', $sort);
                $query->setParameter('sortName', (string) $mixed->getAttribute($handle));
                $query->setParameter('sortID', $mixed->getCollectionID());
                break;
        }
        $query->andWhere($where);
        $query->setParameter('offset', $offset);
    }


}