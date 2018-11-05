<?php
namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateAddedColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'u.uDateAdded';
    }

    public function getColumnName()
    {
        return t('Date');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Search\ColumnSet\Available', 'getUserDateAdded'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(u.uDateAdded, u.uID) %s (:sortDate, :sortID)', $sort);
        $query->setParameter('sortDate', $mixed->getUserDateAdded()->format('Y-m-d H:i:s'));
        $query->setParameter('sortID', $mixed->getUserID());
        $query->andWhere($where);
    }

}
