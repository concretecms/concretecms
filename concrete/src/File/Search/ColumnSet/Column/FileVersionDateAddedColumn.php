<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileVersionDateAddedColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'fv.fvDateAdded';
    }

    public function getColumnName()
    {
        return t('Modified');
    }

    public function getColumnCallback()
    {
        return array('\Concrete\Core\File\Search\ColumnSet\DefaultSet', 'getFileDateActivated');
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(fv.fvDateAdded, f.fID) %s (:sortDate, :sortID)', $sort);
        $fv = $mixed->getApprovedVersion();
        $query->setParameter('sortDate', $fv->getDateAdded()->format('Y-m-d H:i:s'));
        $query->setParameter('sortID', $mixed->getFileID());
        $query->andWhere($where);
    }

}
