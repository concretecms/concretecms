<?php
namespace Concrete\Core\User\Group\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\User\Group\Group;

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
        $where = sprintf('(if(nt.treeNodeTypeHandle=\'group\', g.gName, n.treeNodeName), n.treeNodeID) %s (:sortName, :sortID)', $sort);
        $name = '';
        if ($mixed->getTreeNodeDisplayName()) {
            $name = $mixed->getTreeNodeDisplayName();
        }
        $query->setParameter('sortName', $name);
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $this->andWhereNotExists($query, $where);
    }

}
