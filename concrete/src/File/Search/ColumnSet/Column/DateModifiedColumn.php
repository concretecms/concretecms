<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateModifiedColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'dateModified';
    }

    public function getColumnName()
    {
        return t('Date Modified');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\File\Search\ColumnSet\Available', 'getDateModified'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(if(nt.treeNodeTypeHandle=\'file\', fv.fvDateAdded, n.dateModified), n.treeNodeID) %s (:sortDate, :sortID)', $sort);
        $date = $mixed->getDateLastModified();
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }
        $query->setParameter('sortDate', $date);
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $this->andWhereNotExists($query, $where);
    }

}
