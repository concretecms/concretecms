<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileVersionSizeColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'fv.fvSize';
    }

    public function getColumnName()
    {
        return t('Size');
    }

    public function getColumnCallback()
    {
        return 'getSize';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(fv.fvSize, f.fID) %s (:sortSize, :sortID)', $sort);
        $query->setParameter('sortSize', $mixed->getFullSize());
        $query->setParameter('sortID', $mixed->getFileID());
        $query->andWhere($where);
    }

}
