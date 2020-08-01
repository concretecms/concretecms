<?php

namespace Concrete\Core\Logging\Search\ColumnSet\Column;

use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class ChannelColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'l.channel';
    }

    public function getColumnName()
    {
        return t('Channel');
    }

    public function getColumnCallback()
    {
        return 'getChannel';
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
        $where = sprintf('l.channel %s :channel', $sort);
        $query->setParameter('channel', $mixed->getChannel());
        $query->andWhere($where);
    }

}
