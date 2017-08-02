<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class DatePublicColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'cv.cvDatePublic';
    }

    public function getColumnName()
    {
        return t('Date');
    }

    public function getColumnCallback()
    {
        return array('\Concrete\Core\Page\Search\ColumnSet\DefaultSet', 'getCollectionDatePublic');
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(cv.cvDatePublic, p.cID) %s (:sortDate, :sortID)', $sort);
        $query->setParameter('sortDate', $mixed->getCollectionDatePublic());
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }

}
