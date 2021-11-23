<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
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
        return ['\Concrete\Core\File\Search\ColumnSet\Available', 'getName'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(if(nt.treeNodeTypeHandle=\'file\', fv.fvTitle, n.treeNodeName), n.treeNodeID) %s (:sortName, :sortID)', $sort);
        $name = '';
        if ($mixed->getTreeNodeDisplayName()) {
            $name = $mixed->getTreeNodeDisplayName();
        }
        $query->setParameter('sortName', $name);
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $this->andWhereNotExists($query, $where);
    }

}
