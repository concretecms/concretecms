<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileVersionFilenameColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'fv.fvFilename';
    }

    public function getColumnName()
    {
        return t('Filename');
    }

    public function getColumnCallback()
    {
        return 'getFilename';
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(fv.fvFilename, f.fID) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getFilename());
        $query->setParameter('sortID', $mixed->getFileID());
        $query->andWhere($where);
    }

}
