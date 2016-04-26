<?php
namespace Concrete\Core\File\Search\Result;

use Concrete\Core\Application\UserInterface\ContextMenu\BulkMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{

    public function getSearchResultBulkMenus()
    {
        $group = new BulkMenu();
        $group->setPropertyName('treeNodeTypeHandle');
        $group->setPropertyValue('file');
        $item = new LinkItem('#', t('Download'));
        $menu = new Menu();
        $menu->addItem($item);
        $group->setMenu($menu);
        return $group;
    }

    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);

        return $node;
    }

    public function getColumnDetails($column)
    {
        $node = new Column($this, $column);

        return $node;
    }
}
