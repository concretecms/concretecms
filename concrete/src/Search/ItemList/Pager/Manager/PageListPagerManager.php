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

    public function filterQueryAtOffset(PagerProviderInterface $itemList, Column $column, $sort, $mixed)
    {
        $query = $itemList->getQueryObject();
        $where = sprintf('%s %s :offset', $column->getKey(), $sort);
        switch ($column->getKey()) {
            case 'cvDatePublic':
                $offset = $mixed->getCollectionDatePublic();
                break;
            case 'p.cDisplayOrder':
                $offset = $mixed->getCollectionDisplayOrder();
                break;
            case 'cDateModified':
                $offset = $mixed->getCollectionDateLastModified();
                break;
            case 'cvName':
                $where = sprintf('WEIGHT_STRING(%s) %s WEIGHT_STRING(:offset)', $column->getKey(), $sort);
                $offset = $mixed->getCollectionName();
                break;
            default:
                // Attribute
                $handle = substr($column->getKey(), 3);
                $offset = (string) $mixed->getAttribute($handle);

        }
        $query->andWhere($where);
        $query->setParameter('offset', $offset);
    }


}