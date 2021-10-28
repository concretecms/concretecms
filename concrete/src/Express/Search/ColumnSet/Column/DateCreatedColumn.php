<?php
namespace Concrete\Core\Express\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DateCreatedColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'e.exEntryDateCreated';
    }

    public function getColumnName()
    {
        return t('Date Created');
    }

    public function getColumnCallback()
    {
        return array('\Concrete\Core\Express\Search\ColumnSet\DefaultSet', 'getDateAdded');
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $entry)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(e.exEntryDateCreated, e.exEntryID) %s (:sortDate, :sortID)', $sort);
        $query->setParameter('sortDate', $entry->getDateCreated()->format('Y-m-d H:i:s'));
        $query->setParameter('sortID', $entry->getID());
        $this->andWhereNotExists($query, $where);
    }

}
