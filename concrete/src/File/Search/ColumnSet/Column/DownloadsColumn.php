<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DownloadsColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'totalDownloads';
    }

    public function getColumnName()
    {
        return t('Downloads');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\File\Search\ColumnSet\Available', 'getDownloads'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('totalDownloads %s :sortDownloads', $sort);
        $query->setParameter('sortDownloads', $mixed->getTotalDownloads());
        $this->andWhereNotExists($query, $where);
    }

}
