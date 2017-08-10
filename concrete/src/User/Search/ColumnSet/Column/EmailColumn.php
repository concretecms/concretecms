<?php
namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class EmailColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'u.uEmail';
    }

    public function getColumnName()
    {
        return t('Email');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Search\ColumnSet\Available', 'getUserEmail'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(u.uEmail, u.uID) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getUserEmail());
        $query->setParameter('sortID', $mixed->getUserID());
        $query->andWhere($where);
    }

}
