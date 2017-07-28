<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateLastModifiedColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'c.cDateModified';
    }

    public function getColumnName()
    {
        return t('Last Modified');
    }

    public function getColumnCallback()
    {
        return array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionDateModified');
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(c.cDateModified, p.cID) %s (:sortDate, :sortID)', $sort);
        $query->setParameter('sortDate', $mixed->getCollectionDateLastModified());
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }

}
