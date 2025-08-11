<?php

namespace Concrete\Core\Logging\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\User\UserInfo;

class PageColumn extends Column implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'l.cID';
    }

    public function getColumnName()
    {
        return t('Page');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\Logging\Search\ColumnSet\DefaultSet', 'getPage'];
    }

    /**
     * @param LogList $itemList
     * @param $mixed LogEntry
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        if ($mixed->getPage() instanceof Page) {
            $query = $itemList->getQueryObject();
            $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
            $where = sprintf('l.cID %s :cID', $sort);
            $query->setParameter('uID', $mixed->getPage()->getCollectionID());
            $this->andWhereNotExists($query, $where);
        }
    }

}
