<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\ColumnSet\Available;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
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
        return new FolderSet();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('treeNodeID', $direction);
    }



}