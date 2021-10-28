<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Tree\Node\Node;

class FolderItemListPagerManager extends AbstractPagerManager
{

    public function getCursorStartValue($mixed)
    {
        return $mixed->getTreeNodeId();
    }

    public function getCursorObject($cursor)
    {
        return Node::getByID($cursor);
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('treeNodeID', $direction);
    }



}
