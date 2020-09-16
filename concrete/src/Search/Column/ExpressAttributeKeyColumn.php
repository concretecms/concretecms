<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class ExpressAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $db = \Database::connection();
        $category = $this->attributeKey->getAttributeCategory();
        $value = $db->GetOne('select ' . $this->getColumnKey() . ' from ' . $category->getIndexedSearchTable() . ' where exEntryID = ?', [$mixed->getID()]);
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', e.exEntryID) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', $value);
        $query->setParameter('sortID', $mixed->getUserID());
        $query->andWhere($where);
    }
}
