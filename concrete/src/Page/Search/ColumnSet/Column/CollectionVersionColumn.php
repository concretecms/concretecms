<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class CollectionVersionColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'cv.cvName';
    }

    public function getColumnName()
    {
        return t('Name');
    }

    public function getColumnCallback()
    {
        return 'getCollectionName';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(cv.cvName, p.cID) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getCollectionName());
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }

}
