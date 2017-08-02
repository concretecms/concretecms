<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class PageIDColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'p.cID';
    }

    public function getColumnName()
    {
        return t('ID');
    }

    public function getColumnCallback()
    {
        return 'getCollectionID';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('p.cID %s :sortID', $sort);
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }

}
