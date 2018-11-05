<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FolderItemModifiedColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'folderItemModified';
    }

    public function getColumnName()
    {
        return t('Date Modified');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getDateModified'];
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
        $query->andWhere($where);
    }

}
