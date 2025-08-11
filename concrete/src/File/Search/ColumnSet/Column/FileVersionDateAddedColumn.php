<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileVersionDateAddedColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'fvDateAdded';
    }

    public function getColumnName()
    {
        return t('Date Added');
    }

    public function getColumnCallback()
    {
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(fv.fvDateAdded, f.fID) %s (:sortDate, :sortID)', $sort);
        $date = $mixed->getDateAdded();
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }
        $query->setParameter('sortDate', $date);
        $query->setParameter('sortID', $mixed->getFile()->getFileID());
        $this->andWhereNotExists($query, $where);
    }

}