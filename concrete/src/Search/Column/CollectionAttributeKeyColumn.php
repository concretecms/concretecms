<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class CollectionAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $db = \Database::connection();
        $value = $db->GetOne('select ' . $this->getColumnKey() . ' from CollectionSearchIndexAttributes where cID = ?', [$mixed->getCollectionID()]);
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', p.cID) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', $value);
        $query->setParameter('sortID', $mixed->getCollectionID());
        $query->andWhere($where);
    }
}
