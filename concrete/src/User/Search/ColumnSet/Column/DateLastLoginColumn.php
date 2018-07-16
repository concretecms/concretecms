<?php
namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateLastLoginColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'u.uLastLogin';
    }

    public function getColumnName()
    {
        return t('Last Login');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Search\ColumnSet\Available', 'getUserDateLastLogin'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(u.uLastLogin, u.uID) %s (:sortDate, :sortID)', $sort);
        $date = $mixed->getLastLogin();
        $query->setParameter('sortDate', $date);
        $query->setParameter('sortID', $mixed->getUserID());
        $query->andWhere($where);
    }

}
