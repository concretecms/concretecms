<?php

namespace Concrete\Core\Logging\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use DateTime;

class TimeColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'l.time';
    }

    public function getColumnName()
    {
        return t('Time');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\Logging\Search\ColumnSet\DefaultSet', 'getCollectionTime'];
    }

    /**
     * @param LogList $itemList
     * @param $mixed LogEntry
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        if ($mixed->getTime() instanceof DateTime) {
            $query = $itemList->getQueryObject();
            $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
            $where = sprintf('l.time %s :time', $sort);
            $query->setParameter('time', $mixed->getTime()->getTimestamp());
            $this->andWhereNotExists($query, $where);
        }
    }

}
