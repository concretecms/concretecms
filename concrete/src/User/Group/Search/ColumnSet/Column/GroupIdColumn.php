<?php
namespace Concrete\Core\User\Group\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class GroupIdColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'gID';
    }

    public function getColumnName()
    {
        return t('Group ID');
    }

    public function getColumnCallback()
    {
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('gID %s :sortID', $sort);
        $query->setParameter('sortID', $mixed->getGroupID());
        $this->andWhereNotExists($query, $where);
    }

}