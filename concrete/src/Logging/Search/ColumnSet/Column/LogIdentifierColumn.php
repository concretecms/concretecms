<?php

namespace Concrete\Core\Logging\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class LogIdentifierColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'l.logID';
    }

    public function getColumnName()
    {
        return t('ID');
    }

    public function getColumnCallback()
    {
        return 'getId';
    }

    /**
     * @param LogList $itemList
     * @param $mixed LogEntry
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('l.logID %s :logID', $sort);
        $query->setParameter('logID', $mixed->getId());
        $this->andWhereNotExists($query, $where);
    }

}
