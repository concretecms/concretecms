<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FolderItemTypeColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'folderItemType';
    }

    public function getColumnName()
    {
        return t('Type');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\File\Search\ColumnSet\FolderSet', 'getType'];
    }

    protected function getTypeValue($mixed)
    {
        switch ($mixed->getTreeNodeTypeHandle()) {
            case 'file_folder':
                return 2;
            case 'search_preset':
                return 1;
            case 'file':
                $file = $mixed->getTreeNodeFileObject();
                if (is_object($file)) {
                    $type = $file->getTypeObject();
                    if (is_object($type)) {
                        return $type->getGenericType() + 10;
                    }
                }
        }

    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(case when nt.treeNodeTypeHandle=\'search_preset\' then 1 when nt.treeNodeTypeHandle=\'file_folder\' then 2 else (10 + fvType) end, n.treeNodeID) %s (:sortType, :sortID)', $sort);
        $query->setParameter('sortType', $this->getTypeValue($mixed));
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $query->andWhere($where);
    }

}
