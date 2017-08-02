<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class SitemapDisplayOrderColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'p.cDisplayOrder';
    }

    public function getColumnName()
    {
        return t('Display Order');
    }

    public function getColumnCallback()
    {
        return 'getCollectionDisplayOrder';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(p.cDisplayOrder, p.cID) %s (:sortOrder, :sortID)', $sort);
        $query->setParameter('sortOrder', $mixed->getCollectionDisplayOrder());
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }

}
