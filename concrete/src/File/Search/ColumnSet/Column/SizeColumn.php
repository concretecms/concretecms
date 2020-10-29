<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class SizeColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'size';
    }

    public function getColumnName()
    {
        return t('Size');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\File\Search\ColumnSet\Available', 'getSize'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(fv.fvSize, n.treeNodeID) %s (:sortSize, :sortID)', $sort);
        $size = 0;
        if ($mixed->getTreeNodeTypeHandle() == 'file') {
            $file = $mixed->getTreeNodeFileObject();
            if (is_object($file)) {
                $size = $file->getFullSize();
            }
        }
        $query->setParameter('sortSize', $size);
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $this->andWhereNotExists($query, $where);
    }

}
