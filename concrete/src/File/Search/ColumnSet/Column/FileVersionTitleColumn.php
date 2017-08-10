<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileVersionTitleColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'fv.fvTitle';
    }

    public function getColumnName()
    {
        return t('Name');
    }

    public function getColumnCallback()
    {
        return 'getTitle';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(fv.fvTitle, f.fID) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getTitle());
        $query->setParameter('sortID', $mixed->getFileID());
        $query->andWhere($where);
    }

}
