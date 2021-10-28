<?php

namespace Concrete\Core\Logging\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class MessageColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'l.message';
    }

    public function getColumnName()
    {
        return t('Message');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\Logging\Search\ColumnSet\DefaultSet', 'getFormattedMessage'];
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
        $where = sprintf('l.message %s :message', $sort);
        $query->setParameter('message', $mixed->getMessage());
        $this->andWhereNotExists($query, $where);
    }

}
