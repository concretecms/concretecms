<?php
namespace Concrete\Core\User\Group\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class TypeColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'type';
    }

    public function getColumnName()
    {
        return t('Type');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Group\Search\ColumnSet\Available', 'getType'];
    }

    protected function getTypeValue($mixed)
    {
        switch ($mixed->getTreeNodeTypeHandle()) {
            case 'group_folder':
                return 2;
            case 'group':
                return 1;
        }

    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(case when nt.treeNodeTypeHandle=\'search_preset\' then 1 when nt.treeNodeTypeHandle=\'group_folder\' then 2 else 1 end, n.treeNodeID) %s (:sortType, :sortID)', $sort);
        $query->setParameter('sortType', $this->getTypeValue($mixed));
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $this->andWhereNotExists($query, $where);
    }

}
