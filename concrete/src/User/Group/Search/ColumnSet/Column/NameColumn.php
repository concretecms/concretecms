<?php
namespace Concrete\Core\User\Group\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class NameColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'name';
    }

    public function getColumnName()
    {
        return t('Name');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Group\Search\ColumnSet\Available', 'getGroupName'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(name) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getGroupName());
        $query->setParameter('sortID', $mixed->getGroupID());
        $this->andWhereNotExists($query, $where);
    }

}
