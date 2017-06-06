<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class CollectionAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', p.cID) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', (string) $mixed->getAttribute($this->getAttributeKey()));
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }
}
