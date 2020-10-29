<?php

namespace Concrete\Core\Logging\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\User\UserInfo;

class UserIdentifierColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'l.uID';
    }

    public function getColumnName()
    {
        return t('User');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\Logging\Search\ColumnSet\DefaultSet', 'getCollectionUser'];
    }

    /**
     * @param LogList $itemList
     * @param $mixed LogEntry
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        if ($mixed->getUser() instanceof UserInfo) {
            $query = $itemList->getQueryObject();
            $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
            $where = sprintf('l.uID %s :uID', $sort);
            $query->setParameter('uID', $mixed->getUser()->getUserID());
            $this->andWhereNotExists($query, $where);
        }
    }

}
