<?php

namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

/**
 * Note: This Column is required to work PageList class with PagerPagination,
 * but applying this search column to PageList class doesn't sort by RAND() function.
 * You need to do it separately. See on_start method of the PageList block type.
 */
class RandomColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnName()
    {
        return t('Random');
    }

    function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('p.cID %s :sortID', $sort);
        $query->setParameter('sortID', $mixed->getCollectionID());
        $this->andWhereNotExists($query, $where);
    }
}