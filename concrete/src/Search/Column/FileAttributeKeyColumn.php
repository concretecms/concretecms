<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $db = \Database::connection();
        $value = $db->GetOne('select ' . $this->getColumnKey() . ' from FileSearchIndexAttributes where fID = ?', [$mixed->getFileID()]);
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', f.fID) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', $value);
        $query->setParameter('sortID', $mixed->getFileID());
        $query->andWhere($where);
    }
}
