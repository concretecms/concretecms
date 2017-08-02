<?php
namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class UsernameColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'u.uName';
    }

    public function getColumnName()
    {
        return t('Username');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Search\ColumnSet\Available', 'getUserName'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(u.uName, u.uID) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getUserName());
        $query->setParameter('sortID', $mixed->getUserID());
        $query->andWhere($where);
    }

}
