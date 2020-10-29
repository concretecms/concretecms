<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class UserAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $db = \Database::connection();
        $value = $db->GetOne('select ' . $this->getColumnKey() . ' from UserSearchIndexAttributes where uID = ?', [$mixed->getUserID()]);
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', u.uID) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', $value);
        $query->setParameter('sortID', $mixed->getUserID());
        $this->andWhereNotExists($query, $where);
    }
}
