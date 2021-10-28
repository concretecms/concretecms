<?php
namespace Concrete\Core\Express\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DisplayOrderColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'e.exEntryDisplayOrder';
    }

    public function getColumnName()
    {
        return t('Display Order');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\Express\Search\ColumnSet\DefaultSet', 'getDisplayOrder'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $entry)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('e.exEntryDisplayOrder %s :sortID', $sort);
        $query->setParameter('sortID', $entry->getEntryDisplayOrder());
        $this->andWhereNotExists($query, $where);
    }

}
