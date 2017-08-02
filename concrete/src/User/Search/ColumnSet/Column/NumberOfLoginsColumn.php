<?php
namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class NumberOfLoginsColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'u.uNumLogins';
    }

    public function getColumnName()
    {
        return t('# Logins');
    }

    public function getColumnCallback()
    {
        return 'getNumLogins';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(u.uNumLogins, u.uID) %s (:sortOrder, :sortID)', $sort);
        $query->setParameter('sortOrder', $mixed->getNumLogins());
        $query->setParameter('sortID', $mixed->getUSerID());
        $query->andWhere($where);
    }

}
